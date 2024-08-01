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

use Tasque\Core\Schedulable\SchedulableInterface;

/**
 * Interface HookInterface.
 *
 * Hooks allow hooking into parts of Tasque and its scheduler.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface HookInterface extends SchedulableInterface
{
    /**
     * Fetches the type of the hook.
     */
    public function getType(): HookType;

    /**
     * Installs the hook so that it will be invoked.
     */
    public function install(): void;

    /**
     * Invokes the hook.
     */
    public function invoke(): void;

    /**
     * Uninstalls the hook so that it will no longer be invoked.
     */
    public function uninstall(): void;
}
