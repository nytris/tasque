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

namespace Tasque\Core\Thread\State;

use Tasque\Core\Exception\ThreadDidNotReturnException;
use Tasque\Core\Exception\ThreadDidNotThrowException;
use Tasque\Core\Exception\ThreadNotTerminatedException;
use Throwable;

/**
 * Interface ThreadStateInterface.
 *
 * Encapsulates the state of a green thread.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ThreadStateInterface
{
    /**
     * Fetches the eventual return value of the thread, if it terminated by returning.
     *
     * @throws ThreadNotTerminatedException When the thread has not yet terminated.
     * @throws ThreadDidNotReturnException When the thread threw rather than returning.
     */
    public function getReturn(): mixed;

    /**
     * Fetches the eventual Throwable thrown by the thread, if it terminated by throwing.
     *
     * @throws ThreadNotTerminatedException When the thread has not yet terminated.
     * @throws ThreadDidNotThrowException When the thread returned a value rather than throwing.
     */
    public function getThrow(): Throwable;

    /**
     * Determines whether the thread is running.
     */
    public function isRunning(): bool;

    /**
     * Determines whether the thread has terminated, either by returning a result or throwing a Throwable.
     */
    public function isTerminated(): bool;
}
