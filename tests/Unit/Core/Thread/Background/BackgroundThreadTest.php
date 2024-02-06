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

namespace Tasque\Tests\Unit\Core\Thread\Background;

use Fiber;
use InvalidArgumentException;
use Mockery\MockInterface;
use RuntimeException;
use Tasque\Core\Exception\ThreadDidNotReturnException;
use Tasque\Core\Exception\ThreadDidNotThrowException;
use Tasque\Core\Exception\ThreadNotTerminatedException;
use Tasque\Core\Scheduler\ThreadSet\ThreadSetInterface;
use Tasque\Core\Thread\Background\BackgroundThread;
use Tasque\Core\Thread\Background\InputInterface;
use Tasque\Core\Thread\Control\InternalControlInterface;
use Tasque\Tests\AbstractTestCase;

/**
 * Class BackgroundThreadTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BackgroundThreadTest extends AbstractTestCase
{
    /** @var Fiber<InternalControlInterface, mixed, mixed, mixed> */
    private Fiber $fiber;
    private MockInterface&InputInterface $input;
    private BackgroundThread $thread;
    private MockInterface&ThreadSetInterface $threadSet;

    public function setUp(): void
    {
        parent::setUp();

        $this->input = mock(InputInterface::class);
        $this->threadSet = mock(ThreadSetInterface::class, [
            'addThread' => null,
        ]);

        $this->fiber = new Fiber(function (InternalControlInterface $threadControl) {
            $command = $threadControl->getInput()->getValue();

            switch ($command) {
                case 'throw':
                    throw new RuntimeException('Bang! from fiber');
                case 'return':
                    return 'my result from fiber';
                case 'suspend':
                    Fiber::suspend('my suspend value');
                default:
                    throw new InvalidArgumentException('Unknown command "' . $command . '"');
            }
        });

        $this->thread = new BackgroundThread($this->threadSet, $this->fiber, $this->input);
    }

    public function testGetInputReturnsTheInput(): void
    {
        static::assertSame($this->input, $this->thread->getInput());
    }

    public function testGetReturnReturnsTheResultFromFiber(): void
    {
        $this->input->allows()
            ->getValue()
            ->andReturn('return');
        $this->thread->start();
        $this->thread->switchTo();

        static::assertSame('my result from fiber', $this->thread->getReturn());
    }

    public function testGetReturnRaisesSpecificExceptionWhenFiberIsStillRunning(): void
    {
        $this->input->allows()
            ->getValue()
            ->andReturn('suspend');
        $this->thread->start();
        $this->thread->switchTo();

        $this->expectException(ThreadNotTerminatedException::class);
        $this->expectExceptionMessage('Background thread has not yet terminated');

        $this->thread->getReturn();
    }

    public function testGetReturnRaisesSpecificExceptionWhenFiberHasThrown(): void
    {
        $this->input->allows()
            ->getValue()
            ->andReturn('throw');
        $this->thread->start();
        $this->thread->switchTo();

        $this->expectException(ThreadDidNotReturnException::class);
        $this->expectExceptionMessage('Background thread threw and did not return a value');

        $this->thread->getReturn();
    }

    public function testGetThrowReturnsTheThrowableFromFiber(): void
    {
        $this->input->allows()
            ->getValue()
            ->andReturn('throw');
        $this->thread->start();
        $this->thread->switchTo();

        static::assertSame('Bang! from fiber', $this->thread->getThrow()->getMessage());
    }

    public function testGetThrowRaisesSpecificExceptionWhenFiberIsStillRunning(): void
    {
        $this->input->allows()
            ->getValue()
            ->andReturn('suspend');
        $this->thread->start();
        $this->thread->switchTo();

        $this->expectException(ThreadNotTerminatedException::class);
        $this->expectExceptionMessage('Background thread has not yet terminated');

        $this->thread->getThrow();
    }

    public function testGetThrowRaisesSpecificExceptionWhenFiberHasReturned(): void
    {
        $this->input->allows()
            ->getValue()
            ->andReturn('return');
        $this->thread->start();
        $this->thread->switchTo();

        $this->expectException(ThreadDidNotThrowException::class);
        $this->expectExceptionMessage('Background thread returned a value and did not throw');

        $this->thread->getThrow();
    }

    public function testIsMainThreadReturnsFalse(): void
    {
        static::assertFalse($this->thread->isMainThread());
    }

    public function testIsRunningReturnsTrueInitially(): void
    {
        static::assertTrue($this->thread->isRunning());
    }

    public function testIsRunningReturnsFalseAfterFiberReturns(): void
    {
        $this->input->allows()
            ->getValue()
            ->andReturn('return');
        $this->thread->start();
        $this->thread->switchTo();

        static::assertFalse($this->thread->isRunning());
    }

    public function testIsRunningReturnsFalseAfterFiberThrows(): void
    {
        $this->input->allows()
            ->getValue()
            ->andReturn('throw');
        $this->thread->start();
        $this->thread->switchTo();

        static::assertFalse($this->thread->isRunning());
    }

    public function testIsShoutingReturnsTrueWhenShouting(): void
    {
        $this->thread->shout();

        static::assertTrue($this->thread->isShouting());
    }

    public function testIsShoutingReturnsFalseWhenNotShouting(): void
    {
        static::assertFalse($this->thread->isShouting());
    }

    public function testIsTerminatedReturnsFalseInitially(): void
    {
        static::assertFalse($this->thread->isTerminated());
    }

    public function testIsTerminatedReturnsTrueAfterFiberReturns(): void
    {
        $this->input->allows()
            ->getValue()
            ->andReturn('return');
        $this->thread->start();
        $this->thread->switchTo();

        static::assertTrue($this->thread->isTerminated());
    }

    public function testIsTerminatedReturnsTrueAfterFiberThrows(): void
    {
        $this->input->allows()
            ->getValue()
            ->andReturn('throw');
        $this->thread->start();
        $this->thread->switchTo();

        static::assertTrue($this->thread->isTerminated());
    }

    public function testJoinJoinsTheThreadViaThreadSet(): void
    {
        $this->threadSet->expects()
            ->joinThread($this->thread)
            ->once();

        $this->thread->join();
    }

    public function testShoutCausesExceptionsToBeRaisedInTheMainThread(): void
    {
        $this->input->allows()
            ->getValue()
            ->andReturn('throw');
        $this->thread->start();

        $this->thread->shout();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Bang! from fiber');

        $this->thread->switchTo();
    }

    public function testShoutCausesExceptionsToStillBeRecorded(): void
    {
        $this->input->allows()
            ->getValue()
            ->andReturn('throw');
        $this->thread->start();

        $this->thread->shout();
        try {
            $this->thread->switchTo();
        } catch (RuntimeException) {}

        static::assertSame('Bang! from fiber', $this->thread->getThrow()->getMessage());
    }

    public function testStartAddsTheThreadToThreadSet(): void
    {
        $this->threadSet->expects()
            ->addThread($this->thread)
            ->once();

        $this->thread->start();
    }
}
