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
use Tasque\Core\Scheduler\ContextSwitch\NTockStrategy;
use Tasque\Core\Scheduler\ThreadSet\ThreadSetInterface;
use Tasque\Tests\AbstractTestCase;

/**
 * Class NTockStrategyTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NTockStrategyTest extends AbstractTestCase
{
    private NTockStrategy $strategy;
    private MockInterface&ThreadSetInterface $threadSet;

    public function setUp(): void
    {
        parent::setUp();

        $this->threadSet = mock(ThreadSetInterface::class, [
            'switchContext' => null,
        ]);

        $this->strategy = new NTockStrategy(3);
    }

    public function testHandleTockDoesNotSwitchContextBeforeIntervalIsReached(): void
    {
        $this->threadSet->expects()
            ->switchContext()
            ->never();

        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
    }

    public function testHandleTockSwitchesContextWhenIntervalIsReached(): void
    {
        $this->threadSet->expects()
            ->switchContext()
            ->once();

        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
    }

    public function testHandleTockDoesNotSwitchContextASecondTimeBeforeIntervalIsReached(): void
    {
        $this->threadSet->expects()
            ->switchContext()
            ->once();

        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
    }

    public function testHandleTockSwitchesContextASecondTimeWhenIntervalIsReached(): void
    {
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
