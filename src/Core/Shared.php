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
use Tasque\Core\Scheduler\ContextSwitch\StrategyInterface;
use Tasque\Core\Scheduler\ContextSwitch\TimeSliceStrategy;
use Tasque\Core\Scheduler\Scheduler;
use Tasque\Core\Scheduler\SchedulerInterface;
use Tasque\Core\Scheduler\ThreadSet\FairThreadSet;

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
    private static ?CodeShiftInterface $codeShift = null;
    private static ?SchedulerInterface $scheduler = null;
    private static ?StrategyInterface $schedulerStrategy = null;

    /**
     * Fetches the configured Bootstrap. Will create one by default if not overridden.
     */
    public static function getBootstrap(): BootstrapInterface
    {
        if (self::$bootstrap === null) {
            self::$bootstrap = new Bootstrap(self::getCodeShift());
        }

        return self::$bootstrap;
    }

    /**
     * Fetches the configured CodeShift. Will create one by default if not overridden.
     */
    public static function getCodeShift(): CodeShiftInterface
    {
        if (self::$codeShift === null) {
            self::$codeShift = new CodeShift();
        }

        return self::$codeShift;
    }

    /**
     * Fetches the configured Scheduler. Will create one by default if not overridden.
     */
    public static function getScheduler(): SchedulerInterface
    {
        if (self::$scheduler === null) {
            self::$scheduler = new Scheduler(new FairThreadSet(), self::getSchedulerStrategy());
        }

        return self::$scheduler;
    }

    /**
     * Fetches the configured scheduler strategy. Will create one by default if not overridden.
     */
    public static function getSchedulerStrategy(): StrategyInterface
    {
        if (self::$schedulerStrategy === null) {
            self::$schedulerStrategy = new TimeSliceStrategy();
        }

        return self::$schedulerStrategy;
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
        self::$scheduler = $scheduler;
    }

    /**
     * Overrides the scheduler strategy to use.
     *
     * If null is given, the default implementation will be used.
     */
    public static function setSchedulerStrategy(?StrategyInterface $strategy): void
    {
        self::$scheduler = null;
        self::$schedulerStrategy = $strategy;
    }
}
