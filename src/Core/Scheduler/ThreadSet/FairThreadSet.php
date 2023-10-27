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

use LogicException;
use SplObjectStorage;
use SplQueue;
use Tasque\Core\Thread\BackgroundThreadInterface;
use Tasque\Core\Thread\MainThread;
use Tasque\Core\Thread\ThreadInterface;

/**
 * Class FairThreadSet.
 *
 * Processes threads in round-robin fashion.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FairThreadSet implements ThreadSetInterface
{
    private ThreadInterface $currentThread;
    /**
     * @var SplObjectStorage<ThreadInterface, ThreadInterface>
     */
    private SplObjectStorage $joinedThreadMap;
    private MainThread $mainThread;
    /**
     * @var SplQueue<ThreadInterface>
     */
    private SplQueue $threadQueue;

    public function __construct()
    {
        $this->joinedThreadMap = new SplObjectStorage();
        $this->threadQueue = new SplQueue();

        // Create the special representation of the main thread for scheduling.
        $this->mainThread = new MainThread();
        $this->currentThread = $this->mainThread;
    }

    /**
     * @inheritDoc
     */
    public function addThread(ThreadInterface $thread): void
    {
        $this->threadQueue->enqueue($thread);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentThread(): ThreadInterface
    {
        return $this->currentThread;
    }

    /**
     * @inheritDoc
     */
    public function joinThread(BackgroundThreadInterface $thread): void
    {
        if ($thread->isTerminated()) {
            // Joined thread has already terminated, so there is no need to wait for it.
            return;
        }

        $this->joinedThreadMap->attach($this->currentThread, $thread);

        $this->switchContext();
    }

    /**
     * @inheritDoc
     */
    public function switchContext(): void
    {
        if ($this->threadQueue->isEmpty()) {
            // There is no other thread to switch to.
            return;
        }

        if (!$this->currentThread->isMainThread()) {
            // We're inside a background thread, suspend the current Fiber and return control to the main thread
            // as all scheduling must happen from there.
            $this->currentThread->switchFrom();

            // At this point, the background thread suspended just above will have been resumed.
            return;
        }

        do {
            // Dequeue the next thread.
            $newThread = $this->dequeueNextThread();

            $this->currentThread = $newThread;

            $newThread->switchTo();

            // Once control returns to this point, we must be back in the main thread.

            // Continue processing background threads while the main thread waits on any it has joined.
        } while (!$newThread->isMainThread());
    }

    /**
     * Fetches the next thread from the thread queue that is ready to be scheduled, if any.
     */
    private function dequeueNextThread(): ThreadInterface
    {
        // Add the current thread to the back of the queue for next time.
        $this->threadQueue->enqueue($this->currentThread);

        while (!$this->threadQueue->isEmpty()) {
            $nextThread = $this->threadQueue->dequeue();

            if ($nextThread->isTerminated()) {
                // Thread has terminated; leave it removed from the queue and move to the next one, if any.
                $this->joinedThreadMap->detach($nextThread);

                continue;
            }

            if (!$this->joinedThreadMap->contains($nextThread)) {
                // Thread is not waiting on any other thread, so it can be scheduled.
                return $nextThread;
            }

            $joinedThread = $this->joinedThreadMap[$nextThread];

            if ($joinedThread->isTerminated()) {
                // The thread being waited on has now terminated, so the waiter is now free to be scheduled.
                $this->joinedThreadMap->detach($nextThread);

                return $nextThread;
            }

            // Add the thread to the back of the queue for next time.
            $this->threadQueue->enqueue($nextThread);
        }

        throw new LogicException('No thread is available to be scheduled.');
    }
}
