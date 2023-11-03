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
use Nytris\Core\Package\PackageContextInterface;
use Tasque\Core\Scheduler\ContextSwitch\NTockStrategy;
use Tasque\Core\Shared;
use Tasque\Tasque;
use Tasque\TasquePackageInterface;
use Tasque\Tests\AbstractTestCase;
use Tasque\Tests\Functional\Harness\Log;
use Tasque\Tests\Functional\Harness\SingleBackgroundThread\BackgroundThreadWithDestructor;
use Tasque\Tests\Functional\Harness\SingleBackgroundThread\MainThreadWithDestructorAfterBackgroundThreadStart;
use Tasque\Tests\Functional\Harness\SingleBackgroundThread\MainThreadWithDestructorDuringBackgroundThreadStart;
use Tasque\Tests\Functional\Harness\SingleBackgroundThread\SimpleBackgroundThread;
use Tasque\Tests\Functional\Harness\SingleBackgroundThread\SimpleMainThread;

/**
 * Class SingleBackgroundThreadTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SingleBackgroundThreadTest extends AbstractTestCase
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
        ]);
        $this->packageContext = mock(PackageContextInterface::class);
        $this->tasque = new Tasque();

        Shared::setScheduler(null);

        Tasque::install($this->packageContext, $this->package);
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

        (
            new SimpleMainThread(
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
                'Main thread loop iteration #2',
                'Background thread loop iteration #1',
                'Main thread loop iteration #3',

                // Once joined, the main thread will wait for the background thread to complete before continuing.
                'Before join',
                'Background thread loop iteration #2',
                'Background thread loop iteration #3',
                'End of background thread run',
                'After join',
                'End of main thread run',
            ],
            $this->log->getLog()
        );
    }

    /**
     * Inside a destructor, we cannot switch fibers. We cannot efficiently know whether we are in a context
     * where this is not possible (dispatching of pending signals is another) so we attempt the switch anyway
     * and handle it gracefully (aborting the context switch) if it fails.
     *
     * As the thread remains in the queue, it should successfully be switched to at a later point
     * where the context is not one that forbids switching fibers.
     */
    public function testContextSwitchInsideDestructorInBackgroundThreadIsHandledCorrectly(): void
    {
        $this->log->log('Start');

        (
            new SimpleMainThread(
                $this->tasque,
                $this->log,
                new BackgroundThreadWithDestructor($this->log)
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
                // Note that no switch is performed inside the destructor as that is not possible with Fibers.
                'Main thread loop iteration #1',
                'Before unset() inside background thread',
                'Inside destructor',
                'After unset() inside background thread',
                'End of background thread run',
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

    public function testContextSwitchInsideDestructorInMainThreadAfterBackgroundThreadStartIsHandledCorrectly(): void
    {
        $this->log->log('Start');

        (
            new MainThreadWithDestructorAfterBackgroundThreadStart(
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

                // Main thread (foreground) and background threads are then scheduled evenly.
                // Note that no switch is performed inside the destructor as that is not possible with Fibers.
                'Before unset() inside main thread',
                'Inside destructor',
                'After unset() inside main thread',

                'Start of background thread run',
                'Main thread loop iteration #0',
                'Background thread loop iteration #0',
                'Main thread loop iteration #1',
                'Background thread loop iteration #1',
                'Main thread loop iteration #2',
                'Background thread loop iteration #2',
                'Main thread loop iteration #3',

                // Once joined, the main thread will wait for the background thread to complete before continuing.
                'Before join',
                'Background thread loop iteration #3',
                'End of background thread run',
                'After join',
                'End of main thread run',
            ],
            $this->log->getLog()
        );
    }

    public function testContextSwitchInsideDestructorInMainThreadDuringBackgroundThreadStartIsHandledCorrectly(): void
    {
        $this->log->log('Start');

        (
        new MainThreadWithDestructorDuringBackgroundThreadStart(
            $this->tasque,
            $this->log,
            new SimpleBackgroundThread($this->log)
        )
        )->run();

        static::assertEquals(
            [
                'Start',
                'Start of main thread run',
                // Note that no switch is performed inside the destructor as that is not possible with Fibers.
                'Before unset() inside main thread',
                'Inside destructor',
                'Before background thread start',
                'After background thread start',
                'After unset() inside main thread',
                'Main thread loop iteration #0',
                'Start of background thread run',
                'Main thread loop iteration #1',
                'Background thread loop iteration #0',
                'Main thread loop iteration #2',
                'Background thread loop iteration #1',
                'Main thread loop iteration #3',
                // Once joined, the main thread will wait for the background thread to complete before continuing.
                'Before join',

                // Main thread (foreground) and background threads are then scheduled evenly.
                'Background thread loop iteration #2',
                'Background thread loop iteration #3',
                'End of background thread run',
                'After join',
                'End of main thread run',
            ],
            $this->log->getLog()
        );
    }
}
