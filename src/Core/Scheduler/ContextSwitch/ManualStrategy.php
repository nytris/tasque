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
 * Class ManualStrategy.
 *
 * Context switches between schedulables only when manually asked.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ManualStrategy implements StrategyInterface
{
    /**
     * @inheritDoc
     */
    public function handleTock(SwitchableInterface $switchableContext): void
    {
        // Do nothing: manual strategy will never context-switch automatically,
        // Scheduler->switchContext() must be used.
    }
}
