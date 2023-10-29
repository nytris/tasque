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

namespace Tasque\Tests\Unit\Core\Scheduler\ContextSwitch;

use Mockery\MockInterface;
use Tasque\Core\Clock\ClockInterface;
use Tasque\Core\Scheduler\ContextSwitch\TimeSliceStrategy;
use Tasque\Core\Scheduler\ThreadSet\ThreadSetInterface;
use Tasque\Tests\AbstractTestCase;

/**
 * Class TimeSliceStrategyTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TimeSliceStrategyTest extends AbstractTestCase
{
    private MockInterface&ClockInterface $clock;
    private TimeSliceStrategy $strategy;
    private MockInterface&ThreadSetInterface $threadSet;

    public function setUp(): void
    {
        $this->clock = mock(ClockInterface::class, [
            'getNanoseconds' => 1000, // This is just the initial timestamp.
        ]);
        $this->threadSet = mock(ThreadSetInterface::class, [
            'switchContext' => null,
        ]);

        $this->strategy = new TimeSliceStrategy(1000000, 3, $this->clock);
    }

    public function testHandleTockDoesNotSwitchContextBeforeTockIntervalIsReachedEvenWhenTimeElapsed(): void
    {
        $this->clock->allows()
            ->getNanoseconds()
            ->andReturn(1000 + 1000000);

        $this->threadSet->expects()
            ->switchContext()
            ->never();

        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
    }

    public function testHandleTockDoesNotFetchNanosecondsBeforeTockIntervalIsReached(): void
    {
        $this->clock->expects()
            ->getNanoseconds()
            ->never();

        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
    }

    public function testHandleTockSwitchesContextWhenTockIntervalIsReachedAndTimeElapsed(): void
    {
        $this->clock->allows()
            ->getNanoseconds()
            ->andReturn(1000 + 1000000);

        $this->threadSet->expects()
            ->switchContext()
            ->once();

        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
    }

    public function testHandleTockDoesNotSwitchContextASecondTimeBeforeTockIntervalIsReachedEvenWhenTimeElapsed(): void
    {
        $this->clock->allows()
            ->getNanoseconds()
            ->andReturn(1000 + 1000000)
            ->once();
        $this->clock->allows()
            ->getNanoseconds()
            ->andReturn(1000 + 1000000 + 1000000);

        $this->threadSet->expects()
            ->switchContext()
            ->once();

        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
    }

    public function testHandleTockDoesNotFetchNanosecondsASecondTimeBeforeTockIntervalIsReached(): void
    {
        $this->clock->allows()
            ->getNanoseconds()
            ->andReturn(1000 + 1000000);

        $this->clock->expects()
            ->getNanoseconds()
            ->never();

        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
    }

    public function testHandleTockSwitchesContextASecondTimeWhenTockIntervalIsReachedAndTimeElapsed(): void
    {
        $this->clock->allows()
            ->getNanoseconds()
            ->andReturn(1000 + 1000000)
            ->once();
        $this->clock->allows()
            ->getNanoseconds()
            ->andReturn(1000 + 1000000 + 1000000)
            ->once();

        $this->threadSet->expects()
            ->switchContext()
            ->twice();

        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
    }
}
