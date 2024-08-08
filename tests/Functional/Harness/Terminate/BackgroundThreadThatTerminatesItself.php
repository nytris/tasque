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

use Tasque\Core\Thread\Control\InternalControlInterface;
use Tasque\Tests\Functional\Harness\Log;
use Tasque\Tests\Functional\Harness\TestBackgroundThreadInterface;

/**
 * Class BackgroundThreadThatTerminatesItself.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BackgroundThreadThatTerminatesItself implements TestBackgroundThreadInterface
{
    public function __construct(
        private readonly Log $log,
        private readonly int $terminateAtIteration
    ) {
    }

    public function run(InternalControlInterface $threadControl): void
    {
        $this->log->log('Start of background thread run');

        for ($i = 0; $i < 4; $i++) {
            $this->log->log('Background thread loop iteration #' . $i);

            if ($i === $this->terminateAtIteration) {
                $threadControl->terminate();
            }
        }

        $this->log->log('End of background thread run');
    }
}
