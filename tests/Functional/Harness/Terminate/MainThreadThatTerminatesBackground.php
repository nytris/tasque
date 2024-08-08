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

namespace Tasque\Tests\Functional\Harness\Terminate;

use Tasque\TasqueInterface;
use Tasque\Tests\Functional\Harness\Log;
use Tasque\Tests\Functional\Harness\TestBackgroundThreadInterface;

/**
 * Class MainThreadThatTerminatesBackground.
 *
 * Used by Thread\TerminateTest.
 *
 * Starts a background thread and then later terminates that background thread early,
 * before it has completed its work.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MainThreadThatTerminatesBackground
{
    public function __construct(
        private readonly TasqueInterface $tasque,
        private readonly Log $log,
        private readonly TestBackgroundThreadInterface $backgroundThread
    ) {
    }

    public function run(): void
    {
        $this->log->log('Start of main thread run');

        $backgroundThread = $this->tasque->createThread($this->backgroundThread->run(...));
        $backgroundThread->shout();

        $this->log->log('Before background thread start');
        $backgroundThread->start();
        $this->log->log('After background thread start');

        for ($i = 0; $i < 4; $i++) {
            $this->log->log('Main thread loop iteration #' . $i);

            if ($i === 2) {
                $backgroundThread->terminate();
            }
        }

        $this->log->log('Before join');
        $backgroundThread->join();
        $this->log->log('After join');

        $this->log->log('End of main thread run');
    }
}
