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

namespace Tasque\Tests\Unit\Core\Scheduler;

use Mockery\MockInterface;
use Tasque\Core\Hook\HookSetInterface;
use Tasque\Core\Hook\HookType;
use Tasque\Core\Scheduler\ContextSwitch\StrategyInterface;
use Tasque\Core\Scheduler\Scheduler;
use Tasque\Core\Scheduler\ThreadSet\ThreadSetInterface;
use Tasque\Tests\AbstractTestCase;

/**
 * Class SchedulerTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SchedulerTest extends AbstractTestCase
{
    private MockInterface&HookSetInterface $hookSet;
    private Scheduler $scheduler;
    private MockInterface&StrategyInterface $strategy;
    private MockInterface&ThreadSetInterface $threadSet;

    public function setUp(): void
    {
        parent::setUp();

        $this->hookSet = mock(HookSetInterface::class, [
            'invokeHook' => null,
        ]);
        $this->strategy = mock(StrategyInterface::class, [
            'handleTock' => null,
        ]);
        $this->threadSet = mock(ThreadSetInterface::class);

        $this->scheduler = new Scheduler($this->hookSet, $this->threadSet, $this->strategy);
    }

    public function testGetStrategyReturnsTheStrategy(): void
    {
        static::assertSame($this->strategy, $this->scheduler->getStrategy());
    }

    public function testGetThreadSetReturnsTheThreadSet(): void
    {
        static::assertSame($this->threadSet, $this->scheduler->getThreadSet());
    }

    public function testHandleTockInvokesAnyTockHooks(): void
    {
        $this->hookSet->expects()
            ->invokeHook(HookType::TOCK)
            ->once();

        $this->scheduler->handleTock();
    }

    public function testHandleTockHandlesViaTheStrategy(): void
    {
        $this->strategy->expects()
            ->handleTock($this->threadSet)
            ->once();

        $this->scheduler->handleTock();
    }

    public function testSwitchContextSwitchesViaTheThreadSet(): void
    {
        $this->threadSet->expects()
            ->switchContext()
            ->once();

        $this->scheduler->switchContext();
    }
}
