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

/**
 * Interface TasquePackageInterface.
 *
 * Configures the installation of Tasque.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TasquePackageInterface extends PackageInterface
{
    /**
     * Fetches the configured scheduler strategy to use, if set.
     */
    public function getSchedulerStrategy(): ?StrategyInterface;

    /**
     * Whether to install preemptive context switching via tocks.
     */
    public function isPreemptive(): bool;
}
