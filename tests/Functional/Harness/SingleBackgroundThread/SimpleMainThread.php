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

use Tasque\TasqueInterface;
use Tasque\Tests\Functional\Harness\Log;
use Tasque\Tests\Functional\Harness\SimpleBackgroundThread;

/**
 * Class SimpleMainThread.
 *
 * Used by NTockStrategy\SingleBackgroundThreadTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SimpleMainThread
{
    public function __construct(
        private readonly TasqueInterface $tasque,
        private readonly Log $log
    ) {
    }

    public function run(): void
    {
        $backgroundThread = $this->tasque->createThread((new SimpleBackgroundThread($this->log))->run(...));

        $this->log->log('Before background thread start');
        $backgroundThread->start();
        $this->log->log('After background thread start');

        for ($i = 0; $i < 4; $i++) {
            $this->log->log('Foreground loop iteration #' . $i);
        }

        $this->log->log('Before join');
        $backgroundThread->join();
        $this->log->log('After join');
    }
}
