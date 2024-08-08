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

use Tasque\Core\Thread\Control\InternalControlInterface;
use Tasque\Tests\Functional\Harness\Log;
use Tasque\Tests\Functional\Harness\TestBackgroundThreadInterface;

/**
 * Class TinyBackgroundThread.
 *
 * Used by NTockStrategy tests.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TinyBackgroundThread implements TestBackgroundThreadInterface
{
    public function __construct(
        private readonly Log $log
    ) {
    }

    public function run(InternalControlInterface $threadControl): void
    {
        $this->log->log('Start of background thread run');

        $this->log->log('End of background thread run');
    }
}
