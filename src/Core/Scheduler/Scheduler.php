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
use Tasque\Core\Hook\HookType;
use Tasque\Core\Scheduler\ContextSwitch\StrategyInterface;
use Tasque\Core\Scheduler\ThreadSet\ThreadSetInterface;

/**
 * Class Scheduler.
 *
 * Schedules background threads.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Scheduler implements SchedulerInterface
{
    public function __construct(
        private readonly HookSetInterface $hookSet,
        private readonly ThreadSetInterface $threadSet,
        private readonly StrategyInterface $strategy
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getHookSet(): HookSetInterface
    {
        return $this->hookSet;
    }

    /**
     * @inheritDoc
     */
    public function getStrategy(): StrategyInterface
    {
        return $this->strategy;
    }

    /**
     * @inheritDoc
     */
    public function getThreadSet(): ThreadSetInterface
    {
        return $this->threadSet;
    }

    /**
     * @inheritDoc
     */
    public function handleTock(): void
    {
        $this->hookSet->invokeHook(HookType::TOCK);
        $this->strategy->handleTock($this->threadSet);
    }

    /**
     * @inheritDoc
     */
    public function switchContext(): void
    {
        $this->threadSet->switchContext();
    }
}
