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

use Tasque\Core\Thread\Background\BackgroundThreadInterface;
use Throwable;

/**
 * Class BackgroundThreadState.
 *
 * Encapsulates the state of a background green thread.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BackgroundThreadState implements BackgroundThreadStateInterface
{
    public function __construct(
        private readonly BackgroundThreadInterface $thread
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getReturn(): mixed
    {
        return $this->thread->getReturn();
    }

    /**
     * @inheritDoc
     */
    public function getThrow(): Throwable
    {
        return $this->thread->getThrow();
    }

    /**
     * @inheritDoc
     */
    public function isRunning(): bool
    {
        return $this->thread->isRunning();
    }

    /**
     * @inheritDoc
     */
    public function isShouting(): bool
    {
        return $this->thread->isShouting();
    }

    /**
     * @inheritDoc
     */
    public function isTerminated(): bool
    {
        return $this->thread->isTerminated();
    }

    /**
     * @inheritDoc
     */
    public function join(): void
    {
        $this->thread->join();
    }

    /**
     * @inheritDoc
     */
    public function shout(): void
    {
        $this->thread->shout();
    }

    /**
     * @inheritDoc
     */
    public function start(): void
    {
        $this->thread->start();
    }

    /**
     * @inheritDoc
     */
    public function terminate(): void
    {
        $this->thread->terminate();
    }
}
