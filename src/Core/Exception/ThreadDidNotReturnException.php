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
 * Class ThreadDidNotReturnException.
 *
 * Signifies that a thread terminated by throwing and so there is no return value to fetch.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ThreadDidNotReturnException extends Exception implements TasqueExceptionInterface
{
}
