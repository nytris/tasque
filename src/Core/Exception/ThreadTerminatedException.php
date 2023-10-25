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

namespace Tasque\Core\Exception;

use Exception;

/**
 * Class ThreadTerminatedException.
 *
 * Signifies that a thread has already terminated and so it cannot be scheduled.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ThreadTerminatedException extends Exception implements TasqueExceptionInterface
{
}
