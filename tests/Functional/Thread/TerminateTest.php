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

namespace Tasque\Tests\Functional\Thread;

use Mockery\MockInterface;
use Nytris\Core\Package\PackageContextInterface;
use Tasque\Core\Scheduler\ContextSwitch\NTockStrategy;
use Tasque\Tasque;
use Tasque\TasquePackageInterface;
use Tasque\Tests\AbstractTestCase;
use Tasque\Tests\Functional\Harness\Log;
use Tasque\Tests\Functional\Harness\SingleBackgroundThread\SimpleBackgroundThread;
use Tasque\Tests\Functional\Harness\SingleBackgroundThread\SimpleMainThread;
use Tasque\Tests\Functional\Harness\Terminate\BackgroundThreadThatTerminatesItself;
use Tasque\Tests\Functional\Harness\Terminate\MainThreadThatTerminatesBackground;

/**
 * Class TerminateTest.
 *
 * Tests the background thread and InternalControlInterface `->terminate()` methods.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TerminateTest extends AbstractTestCase
{
    private Log $log;
    private MockInterface&TasquePackageInterface $package;
    private MockInterface&PackageContextInterface $packageContext;
    private Tasque $tasque;

    public function setUp(): void
    {
        $this->log = new Log();
        $this->package = mock(TasquePackageInterface::class, [
            'getSchedulerStrategy' => new NTockStrategy(1),
            'isPreemptive' => true,
        ]);
        $this->packageContext = mock(PackageContextInterface::class);
        $this->tasque = new Tasque();

        Tasque::install($this->packageContext, $this->package);
    }

    public function tearDown(): void
    {
        Tasque::uninstall();
    }

    public function testSingleBackgroundThreadCanBeTerminated(): void
    {
        $this->log->log('Start');

        (
            new MainThreadThatTerminatesBackground(
                $this->tasque,
                $this->log,
                new SimpleBackgroundThread($this->log)
            )
        )->run();

        static::assertEquals(
            [
                'Start',
                'Start of main thread run',
                'Before background thread start',
                'After background thread start',

                'Main thread loop iteration #0',

                'Start of background thread run',

                // Main thread (foreground) and background threads are then scheduled evenly.
                'Main thread loop iteration #1',
                'Background thread loop iteration #0',
                // At this point, the background thread is terminated, so we should see no more logs from it.
                'Main thread loop iteration #2',
                'Main thread loop iteration #3',

                // Once joined, the main thread will wait for the background thread to complete before continuing.
                'Before join',
                'After join',
                'End of main thread run',
            ],
            $this->log->getLog()
        );
    }

    public function testSingleBackgroundThreadCanTerminateItself(): void
    {
        $this->log->log('Start');

        (
            new SimpleMainThread(
                $this->tasque,
                $this->log,
                new BackgroundThreadThatTerminatesItself($this->log, terminateAtIteration: 1)
            )
        )->run();

        static::assertEquals(
            [
                'Start',
                'Start of main thread run',
                'Before background thread start',
                'After background thread start',

                'Main thread loop iteration #0',

                'Start of background thread run',

                // Main thread (foreground) and background threads are then scheduled evenly.
                'Main thread loop iteration #1',
                'Background thread loop iteration #0',
                'Main thread loop iteration #2',
                'Background thread loop iteration #1',
                // At this point, the background thread terminates itself, so we should see no more logs from it.
                'Main thread loop iteration #3',

                // Once joined, the main thread will wait for the background thread to complete before continuing.
                'Before join',
                'After join',
                'End of main thread run',
            ],
            $this->log->getLog()
        );
    }
}
