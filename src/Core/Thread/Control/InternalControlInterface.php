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

use Tasque\Core\Thread\Background\InputInterface;

/**
 * Interface InternalControlInterface.
 *
 * Controls a thread internally (from logic executed inside itself).
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface InternalControlInterface extends ControlInterface
{
    /**
     * Fetches the input for this thread.
     */
    public function getInput(): InputInterface;
}
