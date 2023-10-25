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

namespace Tasque\Core\Marshaller;

use Asmblah\PhpCodeShift\Attribute\Tockless;
use Tasque\Core\Shared;

/**
 * Class Marshaller.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Marshaller
{
    /**
     * Handles a tock call from transpiled code.
     */
    #[Tockless]
    public static function tock(): void
    {
        Shared::getScheduler()->handleTock();
    }
}
