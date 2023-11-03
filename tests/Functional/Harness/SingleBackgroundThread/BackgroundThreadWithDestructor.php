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

use Tasque\Tests\Functional\Harness\Log;

/**
 * Class BackgroundThreadWithDestructor.
 *
 * Used by NTockStrategy tests.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BackgroundThreadWithDestructor implements TestBackgroundThreadInterface
{
    public function __construct(
        private readonly Log $log
    ) {
    }

    public function run(): void
    {
        $this->log->log('Start of background thread run');

        $myObject = new class($this->log) {
            public function __construct(
                private readonly Log $log
            ) {
            }

            public function __destruct()
            {
                $this->log->log('Inside destructor');
            }
        };

        $this->log->log('Before unset() inside background thread');
        unset($myObject);
        $this->log->log('After unset() inside background thread');

        $this->log->log('End of background thread run');
    }
}
