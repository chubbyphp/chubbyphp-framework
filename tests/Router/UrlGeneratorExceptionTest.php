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

    public function testCreateForNotFound(): void
    {
        $exception = UrlGeneratorException::createForMissingParameter();

        self::assertSame('Missing parameters', $exception->getMessage());
        self::assertSame(1, $exception->getCode());
    }

    public function testCreateForMethodNotAllowed(): void
    {
        $exception = UrlGeneratorException::createForInvalidParameter(
            'id',
            '97a2c854-322a-4d0e-bd49-2e378d497919',
            '\d+'
        );

        self::assertSame(
            'Parameter "id" with value "97a2c854-322a-4d0e-bd49-2e378d497919" does not match "\d+"',
            $exception->getMessage()
        );
        self::assertSame(2, $exception->getCode());
    }
}
