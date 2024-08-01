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

namespace Tasque;

use Asmblah\PhpCodeShift\Shifter\Filter\FileFilterInterface;
use Nytris\Core\Package\PackageFacadeInterface;
use Tasque\Core\Hook\HookInterface;
use Tasque\Core\Scheduler\ContextSwitch\PromiscuousStrategy;
use Tasque\Core\Scheduler\ContextSwitch\StrategyInterface;
use Tasque\Core\Thread\Background\InputInterface;
use Tasque\Core\Thread\State\BackgroundThreadStateInterface;

/**
 * Interface TasqueInterface.
 *
 * Defines the public facade API for the library.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TasqueInterface extends PackageFacadeInterface
{
    /**
     * Creates but does not yet install a tock hook.
     *
     * Tock hooks are able to specify their own scheduling strategy, using the same mechanism
     * as thread scheduling but configured separately. By default, a PromiscuousStrategy
     * will be used, so that the callback is invoked after every tock.
     */
    public function createTockHook(
        callable $callback,
        StrategyInterface $switchingStrategy = new PromiscuousStrategy()
    ): HookInterface;

    /**
     * Creates but does not yet start a background thread.
     *
     * Optionally, an input to the thread may be specified.
     */
    public function createThread(callable $callback, ?InputInterface $input = null): BackgroundThreadStateInterface;

    /**
     * Excludes the given Composer package from being transpiled with tock handling.
     */
    public static function excludeComposerPackage(string $packageName): void;

    /**
     * Excludes the given files from being transpiled with tock handling.
     */
    public static function excludeFiles(FileFilterInterface $fileFilter): void;

    /**
     * Manually switches to the next context (thread).
     *
     * Usually, the configured scheduler Strategy will handle this automatically,
     * but ManualStrategy for example requires this method to be called when applicable.
     */
    public static function switchContext(): void;
}
