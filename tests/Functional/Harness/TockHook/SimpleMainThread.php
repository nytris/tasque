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

namespace Tasque\Tests\Functional\Harness\TockHook;

use Tasque\Tests\Functional\Harness\Log;

/**
 * Class SimpleMainThread.
 *
 * Used by Hook\TockHook\SingleTockHookTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SimpleMainThread
{
    public function __construct(
        private readonly Log $log
    ) {
    }

    public function run(): void
    {
        $this->log->log('Start of main thread run');

        for ($i = 0; $i < 4; $i++) {
            $this->log->log('Main thread loop iteration #' . $i);
        }

        $this->log->log('End of main thread run');
    }
}
