<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Router;

use Chubbyphp\Framework\Router\RouteDispatcherException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\RouteDispatcherException
 */
final class RouteDispatcherExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf(
                'Call to private %s::__construct() from context \'%s\'',
                RouteDispatcherException::class,
                RouteDispatcherExceptionTest::class
            )
        );

        new RouteDispatcherException('test');
    }

    public function testCreateForNotFound(): void
    {
        $exception = RouteDispatcherException::createForNotFound('/');

        self::assertSame('https://tools.ietf.org/html/rfc7231#section-6.5.4', $exception->getType());
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
        $exception = RouteDispatcherException::createForMethodNotAllowed('GET', ['POST', 'PUT'], '/');

        self::assertSame('https://tools.ietf.org/html/rfc7231#section-6.5.5', $exception->getType());
        self::assertSame('Method not allowed', $exception->getTitle());
        self::assertSame(
            'Method "GET" at path "/" is not allowed. Must be one of: "POST", "PUT"',
            $exception->getMessage()
        );
        self::assertSame(405, $exception->getCode());
    }
}
