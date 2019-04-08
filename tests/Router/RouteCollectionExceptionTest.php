<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Router;

use Chubbyphp\Framework\Router\RouteCollectionException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\RouteCollectionException
 */
final class RouteCollectionExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf(
                'Call to private %s::__construct() from context \'%s\'',
                RouteCollectionException::class,
                RouteCollectionExceptionTest::class
            )
        );

        new RouteCollectionException('test');
    }

    public function testCreateForNotFound(): void
    {
        $exception = RouteCollectionException::createFreezeException();

        self::assertSame('The route collection is frozen', $exception->getMessage());
        self::assertSame(1, $exception->getCode());
    }
}
