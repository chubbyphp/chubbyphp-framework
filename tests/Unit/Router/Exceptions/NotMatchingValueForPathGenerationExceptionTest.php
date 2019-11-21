<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router\Exceptions;

use Chubbyphp\Framework\Router\Exceptions\NotMatchingValueForPathGenerationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\Exceptions\NotMatchingValueForPathGenerationException
 *
 * @internal
 */
final class NotMatchingValueForPathGenerationExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf(
                'Call to private %s::__construct() from context \'%s\'',
                NotMatchingValueForPathGenerationException::class,
                self::class
            )
        );

        new NotMatchingValueForPathGenerationException('test', 0);
    }

    public function testCreate(): void
    {
        $exception = NotMatchingValueForPathGenerationException::create('name', 'attribute', 'value', 'pattern');

        self::assertSame(
            'Not matching value "value" with pattern "pattern" on attribute "attribute" while'
                .' path generation for route: "name"',
            $exception->getMessage()
        );
        self::assertSame(4, $exception->getCode());
        self::assertSame('name', $exception->getName());
        self::assertSame('attribute', $exception->getAttribute());
        self::assertSame('value', $exception->getValue());
        self::assertSame('pattern', $exception->getPattern());
    }
}
