<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Router;

use Chubbyphp\Framework\Router\InvalidParameter;
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

    public function testCreateForMissingParameters(): void
    {
        $exception = UrlGeneratorException::createForMissingParameters(['id', 'name']);

        self::assertSame('Missing parameters: "id", "name"', $exception->getMessage());
        self::assertSame(2, $exception->getCode());
    }

    public function testCreateForInvalidParameters(): void
    {
        $exception = UrlGeneratorException::createForInvalidParameters([
            new InvalidParameter('id', '97a2c854-322a-4d0e-bd49-2e378d497919', '\d+'),
            new InvalidParameter('name', 'test123', '\[a-z]+'),
        ]);

        self::assertSame(
            'Parameter "id" with value "97a2c854-322a-4d0e-bd49-2e378d497919" does not match "\d+"'.PHP_EOL
                .'Parameter "name" with value "test123" does not match "\[a-z]+"',
            $exception->getMessage()
        );
        self::assertSame(3, $exception->getCode());
    }
}
