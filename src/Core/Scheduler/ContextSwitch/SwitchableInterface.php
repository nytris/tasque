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

namespace Tasque\Core\Scheduler\ContextSwitch;

/**
 * Interface SwitchableInterface.
 *
 * Represents a context that can be switched to.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SwitchableInterface
{
    /**
     * Switches to this context.
     */
    public function switchContext(): void;
}
