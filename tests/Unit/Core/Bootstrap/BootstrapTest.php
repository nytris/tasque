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

namespace Tasque\Tests\Unit\Core\Bootstrap;

use Asmblah\PhpCodeShift\CodeShiftInterface;
use Asmblah\PhpCodeShift\Shifter\Filter\FileFilterInterface;
use Asmblah\PhpCodeShift\Shifter\Shift\Shift\Tock\TockStatementShiftSpec;
use Mockery;
use Mockery\MockInterface;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use Tasque\Core\Bootstrap\Bootstrap;
use Tasque\Core\Marshaller\Marshaller;
use Tasque\Core\Scheduler\ContextSwitch\StrategyInterface;
use Tasque\Core\Shared;
use Tasque\Core\Shutdown\ShutdownHandlerInterface;
use Tasque\TasquePackageInterface;
use Tasque\Tests\AbstractTestCase;

/**
 * Class BootstrapTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BootstrapTest extends AbstractTestCase
{
    private Bootstrap $bootstrap;
    private MockInterface&CodeShiftInterface $codeShift;
    private MockInterface&TasquePackageInterface $package;
    private MockInterface&ShutdownHandlerInterface $shutdownHandler;

    public function setUp(): void
    {
        parent::setUp();

        $this->codeShift = mock(CodeShiftInterface::class, [
            'deny' => null,
            'excludeComposerPackageIfInstalled' => null,
            'install' => null,
            'shift' => null,
            'uninstall' => null,
        ]);
        $this->package = mock(TasquePackageInterface::class, [
            'getSchedulerStrategy' => null,
            'isPreemptive' => true,
        ]);
        $this->shutdownHandler = mock(ShutdownHandlerInterface::class, [
            'install' => null,
        ]);

        $this->bootstrap = new Bootstrap($this->codeShift, $this->shutdownHandler);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->bootstrap->uninstall();
    }

    public function testConstructorAddsDenyRuleForTasqueItself(): void
    {
        $this->codeShift->shouldHaveReceived('deny')
            ->with(Mockery::on(function (FileFilterInterface $fileFilter) {
                return $fileFilter->fileMatches(dirname(__DIR__, 4) . '/src/Tasque.php');
            }))
            ->once();
    }

    public function testConstructorExcludesSymfonyErrorHandlerPackage(): void
    {
        $this->codeShift->shouldHaveReceived('excludeComposerPackageIfInstalled')
            ->with('symfony/error-handler')
            ->once();
    }

    public function testInstallInstallsCodeShift(): void
    {
        $this->codeShift->expects()
            ->install()
            ->once();

        $this->bootstrap->install($this->package);
    }

    public function testInstallAddsATockShiftForMarshallerWhenPreemptive(): void
    {
        $this->codeShift->expects()
            ->shift(Mockery::type(TockStatementShiftSpec::class))
            ->once()
            ->andReturnUsing(function (TockStatementShiftSpec $shiftSpec) {
                /** @var Expression $statementNode */
                $statementNode = $shiftSpec->createStatementNode();

                static::assertInstanceOf(Expression::class, $statementNode);
                /** @var StaticCall $expressionNode */
                $expressionNode = $statementNode->expr;
                static::assertInstanceOf(StaticCall::class, $expressionNode);
                /** @var Name $classNameNode */
                $classNameNode = $expressionNode->class;
                static::assertInstanceOf(Name::class, $classNameNode);
                static::assertSame('\\' . Marshaller::class, $classNameNode->toCodeString());
                /** @var Identifier $methodNameNode */
                $methodNameNode = $expressionNode->name;
                static::assertInstanceOf(Identifier::class, $methodNameNode);
                static::assertSame('tock', $methodNameNode->name);
            });

        $this->bootstrap->install($this->package);
    }

    public function testInstallAddsATockShiftForMarshallerWhenNotPreemptive(): void
    {
        $this->package->allows()
            ->isPreemptive()
            ->andReturnFalse();

        $this->codeShift->expects()
            ->shift(Mockery::type(TockStatementShiftSpec::class))
            ->never();

        $this->bootstrap->install($this->package);
    }

    public function testInstallSetsSchedulerStrategyIfConfigured(): void
    {
        $strategy = mock(StrategyInterface::class, [
            'handleTock' => null,
        ]);
        $this->package->allows()
            ->getSchedulerStrategy()
            ->andReturn($strategy);

        $this->bootstrap->install($this->package);

        static::assertSame($strategy, Shared::getSchedulerStrategy());
    }

    public function testInstallDoesNotFailIfSchedulerStrategyNotConfigured(): void
    {
        $this->expectNotToPerformAssertions();

        $this->bootstrap->install($this->package);
    }

    public function testInstallInstallsShutdownHandler(): void
    {
        $this->shutdownHandler->expects()
            ->install()
            ->once();

        $this->bootstrap->install($this->package);
    }

    public function testIsInstalledReturnsTrueWhenInstalled(): void
    {
        $this->bootstrap->install($this->package);

        static::assertTrue($this->bootstrap->isInstalled());
    }

    public function testIsInstalledReturnsFalseWhenNotInstalled(): void
    {
        static::assertFalse($this->bootstrap->isInstalled());
    }

    public function testUninstallUninstallsCodeShift(): void
    {
        $this->codeShift->expects()
            ->uninstall()
            ->once();

        $this->bootstrap->uninstall();
    }
}
