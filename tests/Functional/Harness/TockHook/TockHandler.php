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
 * Class TockHandler.
 *
 * Used by Hook\TockHook\SingleTockHookTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TockHandler
{
    public function __construct(
        private readonly Log $log
    ) {
    }

    public function handle(): void
    {
        $this->log->log('Inside TockHandler');
    }
}
