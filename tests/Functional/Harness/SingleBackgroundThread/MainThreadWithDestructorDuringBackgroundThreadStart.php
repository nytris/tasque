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

namespace Tasque\Tests\Functional\Harness\SingleBackgroundThread;

use Tasque\Core\Thread\State\BackgroundThreadStateInterface;
use Tasque\Tasque;
use Tasque\TasqueInterface;
use Tasque\Tests\Functional\Harness\Log;

/**
 * Class MainThreadWithDestructorDuringBackgroundThreadStart.
 *
 * Used by NTockStrategy\SingleBackgroundThreadTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MainThreadWithDestructorDuringBackgroundThreadStart
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

        $myObject = new class($this->log, $backgroundThread) {
            public function __construct(
                private readonly Log $log,
                private readonly BackgroundThreadStateInterface $backgroundThread
            ) {
            }

            public function __destruct()
            {
                $this->log->log('Inside destructor');

                $this->log->log('Before background thread start');
                $this->backgroundThread->start();
                $this->log->log('After background thread start');

                Tasque::switchContext();
            }
        };

        $this->log->log('Before unset() inside main thread');
        unset($myObject);
        $this->log->log('After unset() inside main thread');

        for ($i = 0; $i < 4; $i++) {
            $this->log->log('Main thread loop iteration #' . $i);
        }

        $this->log->log('Before join');
        $backgroundThread->join();
        $this->log->log('After join');

        $this->log->log('End of main thread run');
    }
}
