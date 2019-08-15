<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\RouterException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\RouterException
 *
 * @internal
 */
final class RouterExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf(
                'Call to private %s::__construct() from context \'%s\'',
                RouterException::class,
                RouterExceptionTest::class
            )
        );

        new RouterException('test', 0);
    }

    public function testCreateForNotFound(): void
    {
        $exception = RouterException::createForNotFound('/');

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
        $exception = RouterException::createForMethodNotAllowed('GET', ['POST', 'PUT'], '/');

        self::assertSame('https://tools.ietf.org/html/rfc7231#section-6.5.5', $exception->getType());
        self::assertSame('Method not allowed', $exception->getTitle());
        self::assertSame(
            'Method "GET" at path "/" is not allowed. Must be one of: "POST", "PUT"',
            $exception->getMessage()
        );
        self::assertSame(405, $exception->getCode());
    }

    public function testCreateForMissingRoute(): void
    {
        $exception = RouterException::createForMissingRoute('name');

        self::assertSame('Missing route: "name"', $exception->getMessage());
        self::assertSame(1, $exception->getCode());
    }

    public function testCreateForMissingRouteAttribute(): void
    {
        $exception = RouterException::createForMissingRouteAttribute(new \stdClass());

        self::assertSame(
            'Request attribute "route" missing or wrong type "stdClass",'
                .' please add the "Chubbyphp\Framework\Middleware\RouterMiddleware" middleware',
            $exception->getMessage()
        );
        self::assertSame(2, $exception->getCode());
    }
}
