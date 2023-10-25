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
 * Class ThreadNotTerminatedException.
 *
 * Signifies that a thread has not yet terminated and so its return or throw value cannot be fetched.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ThreadNotTerminatedException extends Exception implements TasqueExceptionInterface
{
}
