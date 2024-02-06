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

namespace Tasque\Core\Thread;

use Tasque\Core\Exception\ThreadNotTerminatedException;
use Throwable;

/**
 * Class MainThread.
 *
 * Represents the main application thread.
 * Background threads are run in Fibers on top of the main thread.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MainThread implements MainThreadInterface
{
    /**
     * @inheritDoc
     */
    public function getReturn(): mixed
    {
        throw new ThreadNotTerminatedException('Main thread cannot terminate');
    }

    /**
     * @inheritDoc
     */
    public function getThrow(): Throwable
    {
        throw new ThreadNotTerminatedException('Main thread cannot terminate');
    }

    /**
     * @inheritDoc
     */
    public function isMainThread(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isRunning(): bool
    {
        return true; // Main thread cannot terminate.
    }

    /**
     * @inheritDoc
     */
    public function isTerminated(): bool
    {
        return false; // Main thread cannot terminate.
    }

    /**
     * @inheritDoc
     */
    public function switchFrom(): bool
    {
        // Nothing to do; the main thread's state will remain as part of the current call stack.
        return true;
    }

    /**
     * @inheritDoc
     */
    public function switchTo(): bool
    {
        // Nothing to do; allow execution to return to the main thread up the call stack.
        return true;
    }
}
