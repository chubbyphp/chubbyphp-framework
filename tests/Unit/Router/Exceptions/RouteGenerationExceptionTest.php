<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router\Exceptions;

use Chubbyphp\Framework\Router\Exceptions\RouteGenerationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\Exceptions\RouteGenerationException
 *
 * @internal
 */
final class RouteGenerationExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to private');

        new RouteGenerationException('test', 0);
    }

    public function testCreate(): void
    {
        $previous = new \RuntimeException('Something went wrong');
        $exception = RouteGenerationException::create('name', '/name/{name}', ['name' => 'name'], $previous);

        self::assertSame(
            'Route generation for route "name" with path "/name/{name}" with attributes "{"name":"name"}" failed. Something went wrong',
            $exception->getMessage()
        );
        self::assertSame(3, $exception->getCode());
        self::assertSame('name', $exception->getName());
        self::assertSame('/name/{name}', $exception->getPath());
        self::assertSame('/name/{name}', $exception->getPattern());
        self::assertSame(['name' => 'name'], $exception->getAttributes());
        self::assertSame($previous, $exception->getPrevious());
    }

    public function testCreateWithEmptyAttributes(): void
    {
        $exception = RouteGenerationException::create('name', '/name/{name}', []);

        self::assertSame(
            'Route generation for route "name" with path "/name/{name}" with attributes "{}" failed.',
            $exception->getMessage()
        );
    }
}
