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
use Nytris\Core\Package\PackageInterface;
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
interface TasqueInterface extends PackageInterface
{
    /**
     * Creates but does not yet start a background thread.
     *
     * Optionally, an input to the thread may be specified.
     */
    public function createThread(callable $callback, ?InputInterface $input = null): BackgroundThreadStateInterface;

    /**
     * Excludes the given files from being transpiled with tock handling.
     */
    public function excludeFiles(FileFilterInterface $fileFilter): void;

    /**
     * Sets the Tasque scheduler strategy to use.
     *
     * Intended to be called from nytris.config.php.
     */
    public static function setSchedulerStrategy(?StrategyInterface $strategy): void;

    /**
     * Manually switches to the next context (thread).
     *
     * Usually, the configured scheduler Strategy will handle this automatically,
     * but ManualStrategy for example requires this method to be called when applicable.
     */
    public static function switchContext(): void;
}
