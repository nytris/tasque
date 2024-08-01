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

use LogicException;
use Tasque\Core\Hook\HookSetInterface;
use Tasque\Core\Scheduler\ContextSwitch\StrategyInterface;
use Tasque\Core\Scheduler\ThreadSet\ThreadSetInterface;

/**
 * Class NullScheduler.
 *
 * A no-op scheduler that does nothing.
 * Used when code has been instrumented but Tasque has been uninitialised.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NullScheduler implements SchedulerInterface
{
    public function __construct(
        private readonly HookSetInterface $hookSet,
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
        throw new LogicException('No thread set available: Tasque not initialised');
    }

    /**
     * @inheritDoc
     */
    public function handleTock(): void
    {
        // Do nothing.
    }

    /**
     * @inheritDoc
     */
    public function switchContext(): void
    {
        // Do nothing.
    }
}
