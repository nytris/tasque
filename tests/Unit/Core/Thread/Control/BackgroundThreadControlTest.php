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

namespace Tasque\Tests\Unit\Core\Thread\Control;

use Mockery\MockInterface;
use Tasque\Core\Thread\Background\BackgroundThreadInterface;
use Tasque\Core\Thread\Background\InputInterface;
use Tasque\Core\Thread\Control\BackgroundThreadControl;
use Tasque\Tests\AbstractTestCase;

/**
 * Class BackgroundThreadControlTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BackgroundThreadControlTest extends AbstractTestCase
{
    private BackgroundThreadControl $control;
    private MockInterface&InputInterface $input;
    private MockInterface&BackgroundThreadInterface $thread;

    public function setUp(): void
    {
        $this->input = mock(InputInterface::class);
        $this->thread = mock(BackgroundThreadInterface::class, [
            'getInput' => $this->input,
        ]);

        $this->control = new BackgroundThreadControl($this->thread);
    }

    public function testGetInputFetchesTheInputFromTheThread(): void
    {
        static::assertSame($this->input, $this->control->getInput());
    }

    public function testJoinJoinsTheThread(): void
    {
        $this->thread->expects()
            ->join()
            ->once();

        $this->control->join();
    }
}
