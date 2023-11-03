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
 * Class SimpleBackgroundThread.
 *
 * Used by NTockStrategy tests.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SimpleBackgroundThread implements TestBackgroundThreadInterface
{
    public function __construct(
        private readonly Log $log
    ) {
    }

    public function run(): void
    {
        $this->log->log('Start of background thread run');

        for ($i = 0; $i < 4; $i++) {
            $this->log->log('Background thread loop iteration #' . $i);
        }

        $this->log->log('End of background thread run');
    }
}
