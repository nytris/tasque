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

namespace Tasque\Tests\Functional\Thread\NTockStrategy;

use Mockery\MockInterface;
use Nytris\Core\Package\PackageConfigInterface;
use Tasque\Core\Scheduler\ContextSwitch\NTockStrategy;
use Tasque\Core\Shared;
use Tasque\Tasque;
use Tasque\Tests\AbstractTestCase;
use Tasque\Tests\Functional\Harness\Log;
use Tasque\Tests\Functional\Harness\SingleBackgroundThread\SimpleMainThread;

/**
 * Class SingleBackgroundThreadTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SingleBackgroundThreadTest extends AbstractTestCase
{
    private Log $log;
    private MockInterface&PackageConfigInterface $packageConfig;
    private Tasque $tasque;

    public function setUp(): void
    {
        $this->log = new Log();
        $this->packageConfig = mock(PackageConfigInterface::class);
        $this->tasque = new Tasque();

        Shared::setScheduler(null);
        Shared::setSchedulerStrategy(new NTockStrategy(1));

        Tasque::install($this->packageConfig);
    }

    public function tearDown(): void
    {
        Tasque::uninstall();

        Shared::setScheduler(null);
        Shared::setSchedulerStrategy(null);
    }

    public function testSingleBackgroundThreadIsScheduledCorrectly(): void
    {
        $this->log->log('Start');

        (new SimpleMainThread($this->tasque, $this->log))->run();

        static::assertEquals(
            [
                'Start',
                'Before background thread start',
                'After background thread start',

                'Foreground loop iteration #0',
                // Main thread (foreground) and background threads are then scheduled evenly.
                'Foreground loop iteration #1',
                'Background loop iteration #0',
                'Foreground loop iteration #2',
                'Background loop iteration #1',
                'Foreground loop iteration #3',

                // Once joined, the main thread will wait for the background thread to complete before continuing.
                'Before join',
                'Background loop iteration #2',
                'Background loop iteration #3',
                'After join',
            ],
            $this->log->getLog()
        );
    }
}
