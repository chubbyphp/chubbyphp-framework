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
        $this->expectExceptionMessage('Call to private');

        new NotMatchingValueForPathGenerationException('test', 0);
    }

    public function testCreate(): void
    {
        error_clear_last();

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

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            'Use "Chubbyphp\Framework\Router\Exceptions\RouteGenerationException" instead of'
                .' "Chubbyphp\Framework\Router\Exceptions\NotMatchingValueForPathGenerationException"',
            $error['message']
        );
    }
}
