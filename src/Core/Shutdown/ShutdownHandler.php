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

use Tasque\Tasque;

/**
 * Class ShutdownHandler.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ShutdownHandler implements ShutdownHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function install(): void
    {
        register_shutdown_function(static function () {
            // Make sure that we always switch context at least once per program/request.
            Tasque::switchContext();
        });
    }
}
