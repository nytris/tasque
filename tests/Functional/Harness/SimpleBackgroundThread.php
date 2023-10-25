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

namespace Tasque\Tests\Functional\Harness;

/**
 * Class SimpleBackgroundThread.
 *
 * Used by NTockStrategy tests.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SimpleBackgroundThread
{
    public function __construct(
        private readonly Log $log
    ) {
    }

    public function run(): void
    {
        for ($i = 0; $i < 4; $i++) {
            $this->log->log('Background loop iteration #' . $i);
        }
    }
}