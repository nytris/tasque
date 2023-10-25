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
 * Class ThreadDidNotThrowException.
 *
 * Signifies that a thread terminated by returning a value and so there is no throwable to fetch.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ThreadDidNotThrowException extends Exception implements TasqueExceptionInterface
{
}
