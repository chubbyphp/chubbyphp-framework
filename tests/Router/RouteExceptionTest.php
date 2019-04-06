<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Router;

use Chubbyphp\Framework\Router\RouteException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\RouteException
 */
final class RouteExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf(
                'Call to private %s::__construct() from context \'%s\'',
                RouteException::class,
                RouteExceptionTest::class
            )
        );

        new RouteException('test');
    }

    public function testCreateForNotFound(): void
    {
        $exception = RouteException::createForNotFound('/');

        self::assertSame('Page not found', $exception->getTitle());
        self::assertSame(
            'The page "/" you are looking for could not be found.'
                .' Check the address bar to ensure your URL is spelled correctly.',
            $exception->getMessage()
        );
        self::assertSame(404, $exception->getCode());
    }

    public function testCreateForMethodNotAllowed(): void
    {
        $exception = RouteException::createForMethodNotAllowed('GET', ['POST', 'PUT'], '/');

        self::assertSame('Method not allowed', $exception->getTitle());
        self::assertSame(
            'Method "GET" at path "/" is not allowed. Must be one of: "POST", "PUT"',
            $exception->getMessage()
        );
        self::assertSame(405, $exception->getCode());
    }
}
