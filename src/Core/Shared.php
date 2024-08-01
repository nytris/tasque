<?php

/*
 * Tasque - Run PHP background green threads concurrently.
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/nytris/tasque/
 *
 * Released under the MIT license.
 * https://github.com/nytris/tasque/raw/main/MIT-LICENSE.txt
 */

declare(strict_types=1);

namespace Tasque\Core;

use Asmblah\PhpCodeShift\CodeShift;
use Asmblah\PhpCodeShift\CodeShiftInterface;
use Tasque\Core\Bootstrap\Bootstrap;
use Tasque\Core\Bootstrap\BootstrapInterface;
use Tasque\Core\Hook\HookSet;
use Tasque\Core\Scheduler\ContextSwitch\ManualStrategy;
use Tasque\Core\Scheduler\ContextSwitch\StrategyInterface;
use Tasque\Core\Scheduler\ContextSwitch\TimeSliceStrategy;
use Tasque\Core\Scheduler\NullScheduler;
use Tasque\Core\Scheduler\Scheduler;
use Tasque\Core\Scheduler\SchedulerInterface;
use Tasque\Core\Scheduler\ThreadSet\FairThreadSet;
use Tasque\Core\Shutdown\ShutdownHandler;
use Tasque\Core\Thread\MainThread;
use Tasque\Core\Thread\MainThreadInterface;

/**
 * Class Shared.
 *
 * Manages all services shared between different instances of Tasque.
 *
 * Multiple dependencies of a project may use Tasque, in which case
 * they must all use the same scheduling mechanism, for example.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Shared
{
    private static ?BootstrapInterface $bootstrap = null;
    private static bool $bootstrapped = false;
    private static ?CodeShiftInterface $codeShift = null;
    private static bool $initialised = false;
    private static MainThreadInterface $mainThread;
    private static SchedulerInterface $nullScheduler;
    private static ?SchedulerInterface $scheduler = null;

    /**
     * Bootstrapping only ever happens once, either via Composer's file-autoload mechanism
     * or via Tasque::install(...), whichever happens first.
     */
    public static function bootstrap(): void
    {
        if (self::$bootstrapped) {
            return;
        }

        self::$bootstrapped = true;

        // Create the special representation of the main thread for scheduling.
        self::$mainThread = new MainThread();
        self::$nullScheduler = new NullScheduler(new HookSet(), new ManualStrategy());
    }

    /**
     * Fetches the configured Bootstrap. One will have been created by default if not overridden.
     */
    public static function getBootstrap(): BootstrapInterface
    {
        return self::$bootstrap;
    }

    /**
     * Fetches the configured CodeShift. One will have been created by default if not overridden.
     */
    public static function getCodeShift(): CodeShiftInterface
    {
        return self::$codeShift;
    }

    /**
     * Fetches the null Scheduler.
     */
    public static function getNullScheduler(): SchedulerInterface
    {
        return self::$nullScheduler;
    }

    /**
     * Fetches the configured Scheduler. One will have been created by default if not overridden.
     */
    public static function getScheduler(): SchedulerInterface
    {
        return self::$scheduler;
    }

    /**
     * Fetches the configured scheduler strategy. One will have been created by default if not overridden.
     */
    public static function getSchedulerStrategy(): StrategyInterface
    {
        return self::$scheduler->getStrategy();
    }

    /**
     * Initialises the internal state of Tasque.
     */
    public static function initialise(): void
    {
        if (self::$initialised) {
            return;
        }

        self::$initialised = true;

        self::$codeShift = new CodeShift();
        self::$bootstrap = new Bootstrap(self::$codeShift, new ShutdownHandler());

        // Never transpile core dependencies which should not need tocks applying to them.
        self::$codeShift->excludeComposerPackage('nytris/nytris');

        self::$scheduler = new Scheduler(
            new HookSet(),
            new FairThreadSet(self::$mainThread), new TimeSliceStrategy()
        );
    }

    /**
     * Overrides the Bootstrap to use.
     *
     * If null is given, the default implementation will be used.
     */
    public static function setBootstrap(?BootstrapInterface $bootstrap): void
    {
        self::$bootstrap = $bootstrap;
    }

    /**
     * Overrides the CodeShift to use.
     *
     * If null is given, the default implementation will be used.
     */
    public static function setCodeShift(?CodeShiftInterface $codeShift): void
    {
        self::$codeShift = $codeShift;
    }

    /**
     * Overrides the Scheduler to use.
     *
     * If null is given, the default implementation will be used.
     */
    public static function setScheduler(?SchedulerInterface $scheduler): void
    {
        self::$scheduler = $scheduler ?? new Scheduler(
            self::$scheduler->getHookSet(),
            self::$scheduler->getThreadSet(),
            self::$scheduler->getStrategy()
        );
    }

    /**
     * Overrides the scheduler strategy to use.
     *
     * If null is given, the default implementation will be used.
     */
    public static function setSchedulerStrategy(?StrategyInterface $strategy): void
    {
        self::$scheduler = new Scheduler(
            self::$scheduler->getHookSet(),
            self::$scheduler->getThreadSet(),
            $strategy
        );
    }

    /**
     * Uninitialises the internal state of Tasque.
     *
     * Mostly useful during testing.
     */
    public static function uninitialise(): void
    {
        self::$bootstrap = null;
        self::$codeShift = null;
        // If code has already been instrumented with tocks, we need a stub scheduler available to handle them,
        // even though they will never result in a context switch.
        self::$scheduler = self::$nullScheduler;
        self::$initialised = false;
    }
}
