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

namespace Tasque\Tests\Unit\Core\Marshaller;

use Mockery\MockInterface;
use Tasque\Core\Marshaller\Marshaller;
use Tasque\Core\Scheduler\SchedulerInterface;
use Tasque\Core\Shared;
use Tasque\Tests\AbstractTestCase;

/**
 * Class MarshallerTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MarshallerTest extends AbstractTestCase
{
    private MockInterface&SchedulerInterface $scheduler;

    public function setUp(): void
    {
        $this->scheduler = mock(SchedulerInterface::class);

        Shared::setScheduler($this->scheduler);
    }

    public function tearDown(): void
    {
        Shared::setScheduler(null);
    }

    public function testTockHandlesTockViaTheScheduler(): void
    {
        $this->scheduler->expects()
            ->handleTock()
            ->once();

        Marshaller::tock();
    }
}
