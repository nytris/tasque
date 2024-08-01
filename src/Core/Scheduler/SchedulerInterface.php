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

namespace Tasque\Core\Scheduler;

use Tasque\Core\Hook\HookSetInterface;
use Tasque\Core\Scheduler\ContextSwitch\StrategyInterface;
use Tasque\Core\Scheduler\ThreadSet\ThreadSetInterface;

/**
 * Interface SchedulerInterface.
 *
 * Schedules background threads.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SchedulerInterface
{
    /**
     * Fetches the hook set.
     */
    public function getHookSet(): HookSetInterface;

    /**
     * Fetches the scheduler strategy.
     */
    public function getStrategy(): StrategyInterface;

    /**
     * Fetches the thread set.
     */
    public function getThreadSet(): ThreadSetInterface;

    /**
     * Handles a tock of the application.
     */
    public function handleTock(): void;

    /**
     * Manually switches to the next context (thread).
     *
     * Usually, the configured scheduler Strategy will handle this automatically,
     * but ManualStrategy for example requires this method to be called when applicable.
     */
    public function switchContext(): void;
}
