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

namespace Tasque\Tests\Unit\Core\Thread\Background;

use Tasque\Core\Thread\Background\Input;
use Tasque\Tests\AbstractTestCase;

/**
 * Class InputTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InputTest extends AbstractTestCase
{
    private Input $input;

    public function setUp(): void
    {
        $this->input = new Input('my value');
    }

    public function testGetValueReturnsTheValue(): void
    {
        static::assertSame('my value', $this->input->getValue());
    }
}
