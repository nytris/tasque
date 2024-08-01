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
 * Enum HookType.
 *
 * Hooks allow hooking into parts of Tasque and its scheduler.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
enum HookType: string
{
    case TOCK = 'tock';
}
