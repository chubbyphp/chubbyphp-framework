<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router\Exceptions;

use Chubbyphp\Framework\Router\Exceptions\MissingRouteAttributeOnRequestException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\Exceptions\MissingRouteAttributeOnRequestException
 *
 * @internal
 */
final class MissingRouteAttributeOnRequestExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to private');

        new MissingRouteAttributeOnRequestException('test', 0);
    }

    public function testCreateWithNull(): void
    {
        $route = null;

        $exception = MissingRouteAttributeOnRequestException::create($route);

        self::assertSame(
            'Request attribute "route" missing or wrong type "NULL", please add the'
                .' "Chubbyphp\Framework\Middleware\UrlMatcherMiddleware" middleware',
            $exception->getMessage()
        );
        self::assertSame(2, $exception->getCode());
    }

    public function testCreateWithObject(): void
    {
        $route = new \stdClass();

        $exception = MissingRouteAttributeOnRequestException::create($route);

        self::assertSame(
            'Request attribute "route" missing or wrong type "stdClass", please add the'
                .' "Chubbyphp\Framework\Middleware\UrlMatcherMiddleware" middleware',
            $exception->getMessage()
        );
        self::assertSame(2, $exception->getCode());
    }
}
