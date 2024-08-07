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
 * Class PromiscuousStrategy.
 *
 * Context switches between schedulables every tock.
 * A more performant version of `NTockStrategy(tockInterval: 1)`.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PromiscuousStrategy implements StrategyInterface
{
    /**
     * @inheritDoc
     */
    public function handleTock(SwitchableInterface $switchableContext): void
    {
        $switchableContext->switchContext();
    }
}
