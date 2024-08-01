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

use Tasque\Core\Hook\HookInterface;
use Tasque\Core\Hook\HookSetInterface;
use Tasque\Core\Hook\HookType;
use Tasque\Core\Scheduler\ContextSwitch\StrategyInterface;

/**
 * Class TockHook.
 *
 * A hook that is called for every tock based on its strategy.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TockHook implements HookInterface
{
    public function __construct(
        private readonly HookSetInterface $hookSet,
        private readonly StrategyInterface $switchingStrategy,
        private readonly TockHookContext $switchingContext
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getType(): HookType
    {
        return HookType::TOCK;
    }

    /**
     * @inheritDoc
     */
    public function install(): void
    {
        $this->hookSet->installHook($this);
    }

    /**
     * @inheritDoc
     */
    public function invoke(): void
    {
        $this->switchingStrategy->handleTock($this->switchingContext);
    }

    /**
     * @inheritDoc
     */
    public function uninstall(): void
    {
        $this->hookSet->uninstallHook($this);
    }
}
