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

namespace Tasque\Core\Shutdown;

/**
 * Interface ShutdownHandlerInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ShutdownHandlerInterface
{
    /**
     * Installs any required shutdown handling.
     */
    public function install(): void;
}
