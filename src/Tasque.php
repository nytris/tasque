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

namespace Tasque;

use Asmblah\PhpCodeShift\Shifter\Filter\FileFilterInterface;
use Fiber;
use Nytris\Core\Package\PackageConfigInterface;
use Tasque\Core\Scheduler\ContextSwitch\StrategyInterface;
use Tasque\Core\Shared;
use Tasque\Core\Thread\Background\BackgroundThread;
use Tasque\Core\Thread\Background\Input;
use Tasque\Core\Thread\Background\InputInterface;
use Tasque\Core\Thread\State\BackgroundThreadState;
use Tasque\Core\Thread\State\BackgroundThreadStateInterface;

/**
 * Class Tasque.
 *
 * Defines the public facade API for the library.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Tasque implements TasqueInterface
{
    /**
     * @inheritDoc
     */
    public function createThread(callable $callback, ?InputInterface $input = null): BackgroundThreadStateInterface
    {
        $scheduler = Shared::getScheduler();

        $thread = new BackgroundThread(
            $scheduler->getThreadSet(),
            new Fiber($callback),
            $input ?? new Input(null)
        );

        return new BackgroundThreadState($thread);
    }

    /**
     * @inheritDoc
     */
    public function excludeFiles(FileFilterInterface $fileFilter): void
    {
        Shared::getCodeShift()->deny($fileFilter);
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'tasque';
    }

    /**
     * @inheritDoc
     */
    public static function getVendor(): string
    {
        return 'nytris';
    }

    /**
     * @inheritDoc
     */
    public static function install(PackageConfigInterface $packageConfig): void
    {
        Shared::getBootstrap()->install();
    }

    /**
     * @inheritDoc
     */
    public static function setSchedulerStrategy(?StrategyInterface $strategy): void
    {
        Shared::setSchedulerStrategy($strategy);
    }

    /**
     * @inheritDoc
     */
    public static function switchContext(): void
    {
        Shared::getScheduler()->switchContext();
    }

    /**
     * @inheritDoc
     */
    public static function uninstall(): void
    {
        Shared::getBootstrap()->uninstall();

        Shared::setScheduler(null);
        Shared::setSchedulerStrategy(null);
    }
}
