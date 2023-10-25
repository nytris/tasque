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

use BadMethodCallException;
use Tasque\Core\Scheduler\ThreadSet\ThreadSetInterface;

/**
 * Class TimeSliceStrategy.
 *
 * Schedules background threads based on a time slice limit for each one.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TimeSliceStrategy implements StrategyInterface
{
    /**
     * @inheritDoc
     */
    public function handleTock(ThreadSetInterface $threadSet): void
    {
//        throw new BadMethodCallException(__METHOD__ . ' :: Not yet implemented');
    }
}
