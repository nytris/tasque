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

namespace Tasque\Core\Hook\Tock;

use Tasque\Core\Scheduler\ContextSwitch\SwitchableInterface;
use Tasque\Core\Scheduler\SchedulerInterface;
use Tasque\Core\Shared;

/**
 * Class TockHookContext.
 *
 * Used when invoking the hook from a strategy.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TockHookContext implements SwitchableInterface
{
    public function __construct(
        private readonly SchedulerInterface $nullScheduler,
        /**
         * @var callable
         */
        private $callback
    ) {
    }

    /**
     * @inheritDoc
     */
    public function switchContext(): void
    {
        $scheduler = Shared::getScheduler();

        // Use the null scheduler while invoking the hook to prevent infinite recursion.
        Shared::setScheduler($this->nullScheduler);

        try {
            ($this->callback)();
        } finally {
            Shared::setScheduler($scheduler);
        }
    }
}
