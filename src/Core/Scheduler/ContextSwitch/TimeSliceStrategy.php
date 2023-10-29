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

use Tasque\Core\Clock\Clock;
use Tasque\Core\Clock\ClockInterface;
use Tasque\Core\Scheduler\ThreadSet\ThreadSetInterface;

/**
 * Class TimeSliceStrategy.
 *
 * Schedules background threads based on a time slice limit for each one.
 * Note that we only check the current high-resolution time every N tocks for efficiency.
 *
 * High-resolution timestamp fetching does not invoke the `gettimeofday()` syscall,
 * which would incur an expensive host OS context switch.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TimeSliceStrategy implements StrategyInterface
{
    private readonly ClockInterface $clock;
    private int $currentTock = 0;
    private float|int $currentTimeSliceEndNanoseconds;

    /**
     * @param int $timeSliceLengthNanoseconds Length of a thread time slice in nanoseconds.
     * @param int $timeSliceCheckIntervalTocks The N in "every N tocks, check whether time slice has ended".
     * @param ClockInterface|null $clock Custom clock implementation to use, or null to use the default.
     */
    public function __construct(
        private readonly int $timeSliceLengthNanoseconds = 1000000, // 1 millisecond by default.
        private readonly int $timeSliceCheckIntervalTocks = 100,
        ?ClockInterface $clock = null
    ) {
        $this->clock = $clock ?? new Clock();

        $this->currentTimeSliceEndNanoseconds = $this->clock->getNanoseconds() + $timeSliceLengthNanoseconds;
    }

    /**
     * @inheritDoc
     */
    public function handleTock(ThreadSetInterface $threadSet): void
    {
        $this->currentTock = ($this->currentTock + 1) % $this->timeSliceCheckIntervalTocks;

        if ($this->currentTock === 0) {
            $currentNanoseconds = $this->clock->getNanoseconds();

            if ($currentNanoseconds >= $this->currentTimeSliceEndNanoseconds) {
                // End of current time slice has been reached.

                // Calculate the end of the new time slice based on the actual time we are switching.
                $this->currentTimeSliceEndNanoseconds = $currentNanoseconds + $this->timeSliceLengthNanoseconds;

                $threadSet->switchContext();
            }
        }
    }
}
