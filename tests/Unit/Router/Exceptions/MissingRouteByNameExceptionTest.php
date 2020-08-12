<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router\Exceptions;

use Chubbyphp\Framework\Router\Exceptions\MissingRouteByNameException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\Exceptions\MissingRouteByNameException
 *
 * @internal
 */
final class MissingRouteByNameExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to private');

        new MissingRouteByNameException('test', 0);
    }

    public function testCreate(): void
    {
        $exception = MissingRouteByNameException::create('name');

        self::assertSame('Missing route: "name"', $exception->getMessage());
        self::assertSame(1, $exception->getCode());
        self::assertSame('name', $exception->getName());
    }
}
