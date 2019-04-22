<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Router;

use Chubbyphp\Framework\Router\UrlGeneratorException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\UrlGeneratorException
 */
final class UrlGeneratorExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf(
                'Call to private %s::__construct() from context \'%s\'',
                UrlGeneratorException::class,
                UrlGeneratorExceptionTest::class
            )
        );

        new UrlGeneratorException('test');
    }

    public function testCreateForMissingRoute(): void
    {
        $exception = UrlGeneratorException::createForMissingRoute('name');

        self::assertSame('Missing route: "name"', $exception->getMessage());
        self::assertSame(1, $exception->getCode());
    }
}
