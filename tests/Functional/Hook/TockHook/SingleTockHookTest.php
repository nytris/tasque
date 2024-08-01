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

namespace Tasque\Tests\Functional\Hook\TockHook;

use Mockery\MockInterface;
use Nytris\Core\Package\PackageContextInterface;
use Tasque\Core\Scheduler\ContextSwitch\NTockStrategy;
use Tasque\Tasque;
use Tasque\TasquePackageInterface;
use Tasque\Tests\AbstractTestCase;
use Tasque\Tests\Functional\Harness\Log;
use Tasque\Tests\Functional\Harness\TockHook\SimpleMainThread;

/**
 * Class SingleTockHookTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SingleTockHookTest extends AbstractTestCase
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

        Tasque::install($this->packageContext, $this->package);
    }

    public function tearDown(): void
    {
        Tasque::uninstall();
    }

    public function testTockHookIsScheduledCorrectlyWithinMainThreadWithDefaultPromiscuousStrategy(): void
    {
        $this->log->log('Start');

        $this->log->log('Before hook creation');
        $hook = $this->tasque->createTockHook(function () {
            $this->log->log('Tock hook invoked');
        });
        $this->log->log('After hook creation');

        $this->log->log('Before hook installation');
        $hook->install();
        $this->log->log('After hook installation');

        (
            new SimpleMainThread($this->log)
        )->run();

        static::assertEquals(
            [
                'Start',

                'Before hook creation',
                'After hook creation',
                'Before hook installation',
                'After hook installation',
                'Tock hook invoked',
                'Tock hook invoked',

                'Start of main thread run',
                'Tock hook invoked',

                'Main thread loop iteration #0',
                'Tock hook invoked',
                'Main thread loop iteration #1',
                'Tock hook invoked',
                'Main thread loop iteration #2',
                'Tock hook invoked',
                'Main thread loop iteration #3',

                'End of main thread run',
            ],
            $this->log->getLog()
        );
    }

    public function testTockHookIsScheduledCorrectlyWithinMainThreadWithNTockStrategy(): void
    {
        $this->log->log('Start');

        $this->log->log('Before hook creation');
        $hook = $this->tasque->createTockHook(
            function () {
                $this->log->log('Tock hook invoked');
            },
            new NTockStrategy(2)
        );
        $this->log->log('After hook creation');

        $this->log->log('Before hook installation');
        $hook->install();
        $this->log->log('After hook installation');

        (
        new SimpleMainThread($this->log)
        )->run();

        static::assertEquals(
            [
                'Start',

                'Before hook creation',
                'After hook creation',
                'Before hook installation',
                'After hook installation',
                'Tock hook invoked',

                'Start of main thread run',

                'Main thread loop iteration #0',
                'Tock hook invoked',
                'Main thread loop iteration #1',
                'Main thread loop iteration #2',
                'Tock hook invoked',
                'Main thread loop iteration #3',

                'End of main thread run',
            ],
            $this->log->getLog()
        );
    }
}
