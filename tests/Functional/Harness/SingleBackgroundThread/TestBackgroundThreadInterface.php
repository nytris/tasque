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

/**
 * Interface TestBackgroundThreadInterface.
 *
 * Used by NTockStrategy\SingleBackgroundThreadTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TestBackgroundThreadInterface
{
    public function run(): void;
}
