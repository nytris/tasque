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

namespace Tasque\Tests\Functional\Thread\ManualStrategy;

use Nytris\Core\Package\PackageContextInterface;
use Tasque\Core\Scheduler\ContextSwitch\ManualStrategy;
use Tasque\Tasque;
use Tasque\TasquePackageInterface;
use Tasque\Tests\AbstractTestCase;

/**
 * Class SingleBackgroundThreadTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SingleBackgroundThreadTest extends AbstractTestCase
{
    /**
     * @var string[]
     */
    private array $log = [];
    private Tasque $tasque;

    public function setUp(): void
    {
        $this->tasque = new Tasque();

        Tasque::install(
            mock(PackageContextInterface::class),
            mock(TasquePackageInterface::class, [
                'getSchedulerStrategy' => new ManualStrategy(),
            ])
        );
    }

    public function tearDown(): void
    {
        Tasque::uninstall();
    }

    public function testSingleBackgroundThreadIsScheduledCorrectly(): void
    {
        $this->log[] = 'Start';

        $backgroundThread = $this->tasque->createThread(function () {
            for ($i = 0; $i < 3; $i++) {
                $this->log[] = 'Loop iteration #' . $i;

                if ($i >= 1) {
                    $this->log[] = 'Before switch during loop iteration #' . $i;
                    Tasque::switchContext();
                    $this->log[] = 'After switch during loop iteration #' . $i;
                }
            }
        });

        $this->log[] = 'Before start';
        $backgroundThread->start();
        $this->log[] = 'After start';

        $this->log[] = 'Before initial switch';
        Tasque::switchContext();
        $this->log[] = 'After initial switch';

        $this->log[] = 'Before join';
        $backgroundThread->join();
        $this->log[] = 'After join';

        static::assertEquals(
            [
                'Start',
                'Before start',
                'After start',
                'Before initial switch',
                'Loop iteration #0',
                'Loop iteration #1',
                'Before switch during loop iteration #1',
                'After initial switch',
                'Before join',
                'After switch during loop iteration #1',
                'Loop iteration #2',
                'Before switch during loop iteration #2',
                'After switch during loop iteration #2',
                'After join',
            ],
            $this->log
        );
    }
}
