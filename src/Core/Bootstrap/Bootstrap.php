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

namespace Tasque\Core\Bootstrap;

use Asmblah\PhpCodeShift\CodeShiftInterface;
use Asmblah\PhpCodeShift\Shifter\Filter\FileFilter;
use Asmblah\PhpCodeShift\Shifter\Shift\Shift\Tock\TockStatementShiftSpec;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use Tasque\Core\Marshaller\Marshaller;
use Tasque\Core\Shared;
use Tasque\Core\Shutdown\ShutdownHandlerInterface;
use Tasque\TasquePackageInterface;

/**
 * Class Bootstrap.
 *
 * Bootstraps Tasque.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Bootstrap implements BootstrapInterface
{
    private bool $installed = false;

    public function __construct(
        private readonly CodeShiftInterface $codeShift,
        private readonly ShutdownHandlerInterface $shutdownHandler
    ) {
        // Exclude Tasque itself from having tock hooks applied.
        $this->codeShift->deny(new FileFilter(dirname(__DIR__, 3) . '/src/**'));
    }

    /**
     * @inheritDoc
     */
    public function install(TasquePackageInterface $packageConfig): void
    {
        $this->codeShift->install();

        // Register a "tock" shift so that we can preemptively context-switch between threads.
        $this->codeShift->shift(
            new TockStatementShiftSpec(
                fn () => new Expression(
                    new StaticCall(
                        new Name('\\' . Marshaller::class),
                        new Identifier('tock')
                    )
                )
            )
        );

        $schedulerStrategy = $packageConfig->getSchedulerStrategy();

        if ($schedulerStrategy !== null) {
            Shared::setSchedulerStrategy($schedulerStrategy);
        }

        $this->shutdownHandler->install();

        $this->installed = true;
    }

    /**
     * @inheritDoc
     */
    public function isInstalled(): bool
    {
        return $this->installed;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(): void
    {
        $this->codeShift->uninstall();

        $this->installed = false;
    }
}
