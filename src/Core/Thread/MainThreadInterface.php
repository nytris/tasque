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

namespace Tasque\Core\Thread;

/**
 * Interface MainThreadInterface.
 *
 * Represents the main application thread.
 * Background threads are run in Fibers on top of the main thread.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface MainThreadInterface extends ThreadInterface
{
}
