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

namespace Tasque\Tests\Unit\Core\Hook;

use Tasque\Core\Hook\HookSet;
use Tasque\Core\Hook\HookType;
use Tasque\Core\Hook\Tock\TockHook;
use Tasque\Tests\AbstractTestCase;

/**
 * Class HookSetTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HookSetTest extends AbstractTestCase
{
    private HookSet $hookSet;

    public function setUp(): void
    {
        $this->hookSet = new HookSet();
    }

    public function testInvokeHookInvokesInstalledHooks(): void
    {
        $hook1 = mock(TockHook::class, [
            'getType' => HookType::TOCK
        ]);
        $hook2 = mock(TockHook::class, [
            'getType' => HookType::TOCK
        ]);
        $this->hookSet->installHook($hook1);
        $this->hookSet->installHook($hook2);

        $hook1->expects()
            ->invoke()
            ->once();
        $hook2->expects()
            ->invoke()
            ->once();

        $this->hookSet->invokeHook(HookType::TOCK);
    }

    public function testUninstallHookCausesHookToNoLongerBeInvoked(): void
    {
        $hook1 = mock(TockHook::class, [
            'getType' => HookType::TOCK
        ]);
        $hook2 = mock(TockHook::class, [
            'getType' => HookType::TOCK
        ]);
        $this->hookSet->installHook($hook1);
        $this->hookSet->installHook($hook2);

        $this->hookSet->uninstallHook($hook1);

        $hook1->expects('invoke')
            ->never();
        $hook2->expects()
            ->invoke()
            ->once();

        $this->hookSet->invokeHook(HookType::TOCK);
    }
}
