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

namespace Tasque\Core\Thread\Control;

/**
 * Interface ExternalControlInterface.
 *
 * Controls a background thread.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExternalControlInterface extends ControlInterface
{
    /**
     * Makes the background thread also raise any exceptions in the main thread
     * rather than just recording them.
     */
    public function shout(): void;

    /**
     * Starts the thread. Note that this does not necessarily interrupt the running one,
     * depending on the scheduler strategy in use.
     */
    public function start(): void;
}
