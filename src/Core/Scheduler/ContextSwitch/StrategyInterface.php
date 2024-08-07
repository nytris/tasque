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

namespace Tasque\Core\Scheduler\ContextSwitch;

/**
 * Interface StrategyInterface.
 *
 * Schedules background schedulables (threads and hooks).
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StrategyInterface
{
    /**
     * Handles a tock of the application.
     */
    public function handleTock(SwitchableInterface $switchableContext): void;
}
