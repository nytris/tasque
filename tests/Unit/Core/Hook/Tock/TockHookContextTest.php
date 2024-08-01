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

namespace Tasque\Tests\Unit\Core\Hook\Tock;

use Mockery\MockInterface;
use Tasque\Core\Hook\Tock\TockHookContext;
use Tasque\Core\Scheduler\SchedulerInterface;
use Tasque\Core\Shared;
use Tasque\Tests\AbstractTestCase;

/**
 * Class TockHookContextTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TockHookContextTest extends AbstractTestCase
{
    /**
     * @var callable
     */
    private $callback;
    /**
     * @var callable
     */
    private $callbackBehaviour;
    private TockHookContext $context;
    /**
     * @var string[]
     */
    private array $log = [];
    private MockInterface&SchedulerInterface $nullScheduler;

    public function setUp(): void
    {
        $this->callbackBehaviour = function () {
            $this->log[] = 'Callback called';
        };
        $this->callback = function () {
            ($this->callbackBehaviour)();
        };
        $this->nullScheduler = mock(SchedulerInterface::class);

        $this->context = new TockHookContext($this->nullScheduler, $this->callback);
    }

    public function testSwitchContextInvokesTheCallback(): void
    {
        $this->context->switchContext();

        static::assertEquals(['Callback called'], $this->log);
    }

    public function testSwitchContextSetsNullSchedulerWhileInvokingCallback(): void
    {
        $capturedScheduler = null;
        $this->callbackBehaviour = function () use (&$capturedScheduler) {
            $capturedScheduler = Shared::getScheduler();
        };

        $this->context->switchContext();

        static::assertSame($this->nullScheduler, $capturedScheduler);
    }

    public function testSwitchContextRestoresSchedulerAfterInvokingCallback(): void
    {
        $originalScheduler = Shared::getScheduler();

        $this->context->switchContext();

        static::assertSame($originalScheduler, Shared::getScheduler());
    }
}
