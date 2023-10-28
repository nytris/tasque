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

namespace Tasque\Core\Thread\Background;

/**
 * Interface InputInterface.
 *
 * Encapsulates an input argument for the thread.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface InputInterface
{
    /**
     * Fetches the input value.
     */
    public function getValue(): mixed;
}
