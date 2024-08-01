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

namespace Tasque\Core\Hook\Tock;

use Tasque\Core\Scheduler\ContextSwitch\SwitchableInterface;

/**
 * Class TockHookContext.
 *
 * Used when invoking the hook from a strategy.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TockHookContext implements SwitchableInterface
{
    public function __construct(
        /**
         * @var callable
         */
        private $callback
    ) {
    }

    /**
     * @inheritDoc
     */
    public function switchContext(): void
    {
        ($this->callback)();
    }
}
