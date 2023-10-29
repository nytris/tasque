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
 * Class Clock.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Clock implements ClockInterface
{
    /**
     * @inheritDoc
     */
    public function getNanoseconds(): float|int
    {
        return hrtime(true);
    }
}
