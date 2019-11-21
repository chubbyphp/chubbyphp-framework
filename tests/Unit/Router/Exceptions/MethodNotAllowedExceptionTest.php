<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router\Exceptions;

use Chubbyphp\Framework\Router\Exceptions\MethodNotAllowedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\Exceptions\MethodNotAllowedException
 *
 * @internal
 */
final class MethodNotAllowedExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf(
                'Call to private %s::__construct() from context \'%s\'',
                MethodNotAllowedException::class,
                self::class
            )
        );

        new MethodNotAllowedException('test', 0);
    }

    public function testCreare(): void
    {
        $exception = MethodNotAllowedException::create('/', 'GET', ['POST', 'PUT']);

        self::assertSame('https://tools.ietf.org/html/rfc7231#section-6.5.5', $exception->getType());
        self::assertSame('Method not allowed', $exception->getTitle());
        self::assertSame(
            'Method "GET" at path "/" is not allowed. Must be one of: "POST", "PUT"',
            $exception->getMessage()
        );
        self::assertSame(405, $exception->getCode());
    }
}
