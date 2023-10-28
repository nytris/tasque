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

namespace Tasque\Core\Scheduler\ThreadSet;

use Tasque\Core\Thread\Background\BackgroundThreadInterface;
use Tasque\Core\Thread\ThreadInterface;

/**
 * Interface ThreadSetInterface.
 *
 * Contains the set of all running threads.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ThreadSetInterface
{
    /**
     * Adds a thread to the set to be run.
     */
    public function addThread(ThreadInterface $thread): void;

    /**
     * Fetches the current thread.
     */
    public function getCurrentThread(): ThreadInterface;

    /**
     * Makes the current thread wait for the specified background thread to complete before continuing.
     */
    public function joinThread(BackgroundThreadInterface $thread): void;

    /**
     * Switches to the next thread in the set, returning once the current thread has control again.
     */
    public function switchContext(): void;
}
