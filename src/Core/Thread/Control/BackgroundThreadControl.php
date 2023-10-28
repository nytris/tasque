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

use Tasque\Core\Thread\Background\BackgroundThreadInterface;
use Tasque\Core\Thread\Background\InputInterface;

/**
 * Class BackgroundThreadControl.
 *
 * Controls a background thread.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BackgroundThreadControl implements InternalControlInterface
{
    public function __construct(
        private readonly BackgroundThreadInterface $thread
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getInput(): InputInterface
    {
        return $this->thread->getInput();
    }

    /**
     * @inheritDoc
     */
    public function join(): void
    {
        $this->thread->join();
    }
}
