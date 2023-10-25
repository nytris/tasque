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

use Tasque\Core\Scheduler\ThreadSet\ThreadSetInterface;

/**
 * Class NTockStrategy.
 *
 * Context switches between threads every N tocks.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NTockStrategy implements StrategyInterface
{
    private int $currentTock = 0;

    public function __construct(
        private readonly int $tockInterval
    ) {
    }

    /**
     * @inheritDoc
     */
    public function handleTock(ThreadSetInterface $threadSet): void
    {
        $this->currentTock = ($this->currentTock + 1) % $this->tockInterval;

        if ($this->currentTock === 0) {
            $threadSet->switchContext();
        }
    }
}
