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

namespace Tasque;

use Nytris\Core\Package\PackageInterface;
use Tasque\Core\Scheduler\ContextSwitch\StrategyInterface;
use Tasque\Core\Thread\State\BackgroundThreadStateInterface;

/**
 * Interface TasqueInterface.
 *
 * Defines the public facade API for the library.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TasqueInterface extends PackageInterface
{
    /**
     * Creates but does not yet start a background thread.
     */
    public function createThread(callable $callback): BackgroundThreadStateInterface;

    /**
     * Sets the Tasque scheduler strategy to use.
     *
     * Intended to be called from nytris.config.php.
     */
    public static function setSchedulerStrategy(StrategyInterface $strategy): void;
}
