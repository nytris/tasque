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

namespace Tasque\Core\Hook;

/**
 * Class HookSet.
 *
 * Contains the set of all installed hooks.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HookSet implements HookSetInterface
{
    /**
     * @var array<string, array<int, HookInterface>>
     */
    private array $typeToHooks = [];

    /**
     * @inheritDoc
     */
    public function installHook(HookInterface $hook): void
    {
        $this->typeToHooks[$hook->getType()->value][spl_object_id($hook)] = $hook;
    }

    /**
     * @inheritDoc
     */
    public function invokeHook(HookType $hookType): void
    {
        $hooksOfType = $this->typeToHooks[$hookType->value] ?? [];

        foreach ($hooksOfType as $hook) {
            $hook->invoke();
        }
    }

    /**
     * @inheritDoc
     */
    public function uninstallHook(HookInterface $hook): void
    {
        unset($this->typeToHooks[$hook->getType()->value][spl_object_id($hook)]);
    }
}
