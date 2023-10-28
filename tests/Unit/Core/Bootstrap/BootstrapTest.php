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

    public function setUp(): void
    {
        $this->codeShift = mock(CodeShiftInterface::class, [
            'deny' => null,
            'install' => null,
            'shift' => null,
        ]);

        $this->bootstrap = new Bootstrap($this->codeShift);
    }

    public function testConstructorAddsDenyRuleForTasqueItself(): void
    {
        $this->codeShift->shouldHaveReceived('deny')
            ->with(Mockery::on(function (FileFilterInterface $fileFilter) {
                return $fileFilter->fileMatches(dirname(__DIR__, 4) . '/src/Tasque.php');
            }))
            ->once();
    }

    public function testInstallInstallsCodeShift(): void
    {
        $this->codeShift->expects()
            ->install()
            ->once();

        $this->bootstrap->install();
    }

    public function testInstallAddsATockShiftForMarshaller(): void
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

        $this->bootstrap->install();
    }

    public function testUninstallUninstallsCodeShift(): void
    {
        $this->codeShift->expects()
            ->uninstall()
            ->once();

        $this->bootstrap->uninstall();
    }
}
