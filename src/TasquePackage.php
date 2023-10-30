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

use Tasque\Core\Scheduler\ContextSwitch\StrategyInterface;

/**
 * Class TasquePackage.
 *
 * Configures the installation of Tasque.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TasquePackage implements TasquePackageInterface
{
    public function __construct(
        private readonly ?StrategyInterface $schedulerStrategy = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getPackageFacadeFqcn(): string
    {
        return Tasque::class;
    }

    /**
     * @inheritDoc
     */
    public function getSchedulerStrategy(): ?StrategyInterface
    {
        return $this->schedulerStrategy;
    }
}
