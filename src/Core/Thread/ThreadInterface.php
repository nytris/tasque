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

use Tasque\Core\Exception\ThreadTerminatedException;
use Tasque\Core\Thread\State\ThreadStateInterface;
use Throwable;

/**
 * Interface ThreadInterface.
 *
 * Encapsulates a green thread.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ThreadInterface extends ThreadStateInterface
{
    /**
     * Determines whether this is the main thread.
     */
    public function isMainThread(): bool;

    /**
     * Switches execution out of the thread, if it is running.
     * Its running state will be stored as the state of its Fiber if it is a background thread.
     *
     * Always called from a background thread by the ThreadSet.
     */
    public function switchFrom(): bool;

    /**
     * Switches execution to the thread, starting it if it has not been already.
     * Always called from the main thread by the ThreadSet.
     *
     * @throws ThreadTerminatedException When the thread has already terminated.
     * @throws Throwable When the thread has ->shout() enabled and an exception is raised.
     */
    public function switchTo(): bool;
}
