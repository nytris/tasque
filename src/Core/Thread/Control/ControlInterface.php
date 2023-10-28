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
 * Interface ControlInterface.
 *
 * Controls a thread.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ControlInterface
{
    /**
     * Makes the current thread wait for this thread to complete before continuing.
     */
    public function join(): void;
}
