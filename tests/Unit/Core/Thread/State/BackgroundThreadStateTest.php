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

namespace Tasque\Tests\Unit\Core\Thread\State;

use Mockery\MockInterface;
use RuntimeException;
use Tasque\Core\Thread\Background\BackgroundThreadInterface;
use Tasque\Core\Thread\State\BackgroundThreadState;
use Tasque\Tests\AbstractTestCase;

/**
 * Class BackgroundThreadStateTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BackgroundThreadStateTest extends AbstractTestCase
{
    private BackgroundThreadState $state;
    private MockInterface&BackgroundThreadInterface $thread;

    public function setUp(): void
    {
        $this->thread = mock(BackgroundThreadInterface::class, [
            'getReturn' => 'my return value',
            'getThrow' => new RuntimeException('Bang! from thread'),
        ]);

        $this->state = new BackgroundThreadState($this->thread);
    }

    public function testGetReturnFetchesTheReturnValueOfTheThread(): void
    {
        static::assertSame('my return value', $this->state->getReturn());
    }

    public function testGetThrowFetchesTheRaisedThrowableOfTheThread(): void
    {
        $throwable = $this->state->getThrow();

        static::assertEquals(new RuntimeException('Bang! from thread'), $throwable);
    }

    /**
     * @dataProvider booleanProvider
     */
    public function testIsRunningReturnsTheRunningStateOfTheThread(bool $isRunning): void
    {
        $this->thread->allows()
            ->isRunning()
            ->andReturn($isRunning);

        static::assertSame($isRunning, $this->state->isRunning());
    }

    /**
     * @dataProvider booleanProvider
     */
    public function testIsTerminatedReturnsTheTerminatedStateOfTheThread(bool $isTerminated): void
    {
        $this->thread->allows()
            ->isTerminated()
            ->andReturn($isTerminated);

        static::assertSame($isTerminated, $this->state->isTerminated());
    }

    /**
     * @return array{true: array<true>, false: array<false>}
     */
    public static function booleanProvider(): array
    {
        return ['true' => [true], 'false' => [false]];
    }

    /**
     * @dataProvider booleanProvider
     */
    public function testIsShoutingReturnsTheShoutingStateOfTheThread(bool $isShouting): void
    {
        $this->thread->allows()
            ->isShouting()
            ->andReturn($isShouting);

        static::assertSame($isShouting, $this->state->isShouting());
    }

    public function testJoinJoinsTheThread(): void
    {
        $this->thread->expects()
            ->join()
            ->once();

        $this->state->join();
    }

    public function testShoutMarksTheThreadAsShouting(): void
    {
        $this->thread->expects()
            ->shout()
            ->once();

        $this->state->shout();
    }

    public function testStartStartsTheThread(): void
    {
        $this->thread->expects()
            ->start()
            ->once();

        $this->state->start();
    }
}
