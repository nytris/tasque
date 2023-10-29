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

namespace Tasque\Core\Clock;

/**
 * Interface ClockInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ClockInterface
{
    /**
     * Fetches the current high-resolution timestamp in nanoseconds (hrtime()).
     *
     * Return value is float on 32-bit platforms, int on 64-bit.
     */
    public function getNanoseconds(): float|int;
}
