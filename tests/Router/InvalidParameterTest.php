<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Router;

use Chubbyphp\Framework\Router\InvalidParameter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\InvalidParameter
 */
final class InvalidParameterTest extends TestCase
{
    public function testInvalidParameter(): void
    {
        $invalidParameter = new InvalidParameter('id', '97a2c854-322a-4d0e-bd49-2e378d497919', '\d+');

        self::assertSame('id', $invalidParameter->getParameter());
        self::assertSame('97a2c854-322a-4d0e-bd49-2e378d497919', $invalidParameter->getValue());
        self::assertSame('\d+', $invalidParameter->getPattern());
    }
}
