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

namespace Tasque\Core\Thread\State;

use Tasque\Core\Thread\BackgroundThreadControlInterface;

/**
 * Interface BackgroundThreadStateInterface.
 *
 * Encapsulates the state of a background green thread.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BackgroundThreadStateInterface extends BackgroundThreadControlInterface, ThreadStateInterface
{
}
