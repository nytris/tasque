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

namespace Tasque\Tests\Unit\Core\Scheduler\ContextSwitch;

use Mockery\MockInterface;
use Tasque\Core\Scheduler\ContextSwitch\PromiscuousStrategy;
use Tasque\Core\Scheduler\ThreadSet\ThreadSetInterface;
use Tasque\Tests\AbstractTestCase;

/**
 * Class PromiscuousStrategyTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PromiscuousStrategyTest extends AbstractTestCase
{
    private PromiscuousStrategy $strategy;
    private MockInterface&ThreadSetInterface $threadSet;

    public function setUp(): void
    {
        parent::setUp();

        $this->threadSet = mock(ThreadSetInterface::class, [
            'switchContext' => null,
        ]);

        $this->strategy = new PromiscuousStrategy();
    }

    public function testHandleTockSwitchesContext(): void
    {
        $this->threadSet->expects()
            ->switchContext()
            ->once();

        $this->strategy->handleTock($this->threadSet);
    }

    public function testHandleTockSwitchesContextASecondTimeWhenExpected(): void
    {
        $this->threadSet->expects()
            ->switchContext()
            ->twice();

        $this->strategy->handleTock($this->threadSet);
        $this->strategy->handleTock($this->threadSet);
    }
}
