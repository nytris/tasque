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

namespace Tasque\Core\Thread\Background;

use Fiber;
use FiberError;
use LogicException;
use Tasque\Core\Exception\ThreadDidNotReturnException;
use Tasque\Core\Exception\ThreadDidNotThrowException;
use Tasque\Core\Exception\ThreadNotTerminatedException;
use Tasque\Core\Exception\ThreadTerminatedException;
use Tasque\Core\Scheduler\ThreadSet\ThreadSetInterface;
use Tasque\Core\Thread\Control\BackgroundThreadControl;
use Tasque\Core\Thread\Control\InternalControlInterface;
use Throwable;

/**
 * Class BackgroundThread.
 *
 * Encapsulates a background thread.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BackgroundThread implements BackgroundThreadInterface
{
    private const CANNOT_SWITCH_FIBERS_ERROR_MESSAGE = 'Cannot switch fibers in current execution context';
    private const CANNOT_SWITCH_FIBERS_IN_NESTED_THREAD = 'Cannot switch fiber context in a nested thread';

    private ?Throwable $resultThrowable = null;
    /**
     * Whether the background thread should also raise any exceptions in the main thread
     * rather than only recording them.
     */
    private bool $shout = false;

    /**
     * @param ThreadSetInterface $threadSet
     * @param Fiber<InternalControlInterface, mixed, mixed, mixed> $fiber
     * @param InputInterface $input
     */
    public function __construct(
        private readonly ThreadSetInterface $threadSet,
        private readonly Fiber $fiber,
        private readonly InputInterface $input
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @inheritDoc
     */
    public function getReturn(): mixed
    {
        if (!$this->fiber->isTerminated()) {
            throw new ThreadNotTerminatedException('Background thread has not yet terminated');
        }

        if ($this->resultThrowable !== null) {
            throw new ThreadDidNotReturnException('Background thread threw and did not return a value');
        }

        return $this->fiber->getReturn();
    }

    /**
     * @inheritDoc
     */
    public function getThrow(): Throwable
    {
        if (!$this->fiber->isTerminated()) {
            throw new ThreadNotTerminatedException('Background thread has not yet terminated');
        }

        if ($this->resultThrowable === null) {
            throw new ThreadDidNotThrowException('Background thread returned a value and did not throw');
        }

        return $this->resultThrowable;
    }

    /**
     * @inheritDoc
     */
    public function isMainThread(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isRunning(): bool
    {
        // Even if the thread's fiber has not yet been started by the scheduler,
        // consider it running unless it has already terminated.
        return !$this->fiber->isTerminated();
    }

    /**
     * @inheritDoc
     */
    public function isShouting(): bool
    {
        return $this->shout;
    }

    /**
     * @inheritDoc
     */
    public function isTerminated(): bool
    {
        return $this->fiber->isTerminated();
    }

    /**
     * @inheritDoc
     */
    public function join(): void
    {
        $this->threadSet->joinThread($this);
    }

    /**
     * @inheritDoc
     */
    public function shout(): void
    {
        $this->shout = true;
    }

    /**
     * @inheritDoc
     */
    public function start(): void
    {
        $this->threadSet->addThread($this);
    }

    /**
     * @inheritDoc
     */
    public function switchFrom(): bool
    {
        if ($this->fiber->isTerminated()) {
            // Thread not started or has terminated; nothing to do.
            return true;
        }

        if ($this !== $this->threadSet->getCurrentThread()) {
            throw new LogicException('Cannot suspend thread: it is not the current thread');
        }

        if (Fiber::getCurrent() === null) {
            throw new LogicException('Cannot suspend current thread fiber as there is none');
        }

        try {
            Fiber::suspend();
        } catch (FiberError $error) {
            if ($error->getMessage() === self::CANNOT_SWITCH_FIBERS_ERROR_MESSAGE) {
                if ($error->getFile() !== __FILE__ || $error->getLine() !== __LINE__ - 3) {
                    // The error wasn't caused by ->resume() above, so there is a bug somewhere.
                    throw new LogicException(self::CANNOT_SWITCH_FIBERS_IN_NESTED_THREAD);
                }

                // We are unable to switch contexts at this time, so cancel this switch.
                return false;
            }

            throw $error; // Some other unexpected Fiber error.
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function switchTo(): bool
    {
        if (!$this->fiber->isStarted()) {
            $threadControl = new BackgroundThreadControl($this);

            try {
                $this->fiber->start($threadControl);
            } catch (FiberError $error) {
                if ($error->getMessage() === self::CANNOT_SWITCH_FIBERS_ERROR_MESSAGE) {
                    if ($error->getFile() !== __FILE__ || $error->getLine() !== __LINE__ - 3) {
                        // The error wasn't caused by ->resume() above, so there is a bug somewhere.
                        throw new LogicException(self::CANNOT_SWITCH_FIBERS_IN_NESTED_THREAD);
                    }

                    // We are unable to switch contexts at this time, so cancel this switch.
                    return false;
                }

                throw $error; // Some other unexpected Fiber error.
            } catch (Throwable $throwable) {
                // Record the throw, but do not necessarily rethrow it, unless...
                $this->resultThrowable = $throwable;

                if ($this->shout) {
                    throw $throwable;
                }
            }

            return true; // Allow the context switch.
        }

        if ($this->fiber->isTerminated()) {
            // Thread has already terminated.
            throw new ThreadTerminatedException('Thread has already terminated');
        }

        try {
            $this->fiber->resume();
        } catch (FiberError $error) {
            if ($error->getMessage() === self::CANNOT_SWITCH_FIBERS_ERROR_MESSAGE) {
                if ($error->getFile() !== __FILE__ || $error->getLine() !== __LINE__ - 3) {
                    // The error wasn't caused by ->resume() above, so there is a bug somewhere.
                    throw new LogicException(self::CANNOT_SWITCH_FIBERS_IN_NESTED_THREAD);
                }

                // We are unable to switch contexts at this time, so cancel this switch.
                return false;
            }

            throw $error; // Some other unexpected Fiber error.
        } catch (Throwable $throwable) {
            // Record the throw, but do not necessarily rethrow it, unless...
            $this->resultThrowable = $throwable;

            if ($this->shout) {
                throw $throwable;
            }
        }

        return true; // Allow the context switch.
    }
}
