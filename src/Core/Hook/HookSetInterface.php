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
 * Interface HookSetInterface.
 *
 * Contains the set of all installed hooks.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface HookSetInterface
{
    /**
     * Installs a hook.
     */
    public function installHook(HookInterface $hook): void;

    /**
     * Invokes the given type of hook.
     */
    public function invokeHook(HookType $hookType): void;

    /**
     * Uninstalls a hook.
     */
    public function uninstallHook(HookInterface $hook): void;
}
