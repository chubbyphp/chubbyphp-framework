<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\ResponseHandler;

use Chubbyphp\Framework\ExceptionHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\ExceptionHelper
 */
final class ExceptionHelperTest extends TestCase
{
    public function testToArray(): void
    {
        $exceptions = ExceptionHelper::toArray(
            new \RuntimeException('runtime exception', 1, new \LogicException('logic exception', 2))
        );

        self::assertCount(2, $exceptions);

        self::assertSame(\RuntimeException::class, $exceptions[0]['class']);
        self::assertSame('runtime exception', $exceptions[0]['message']);
        self::assertSame(1, $exceptions[0]['code']);
        self::assertArrayHasKey('file', $exceptions[0]);
        self::assertArrayHasKey('line', $exceptions[0]);
        self::assertArrayHasKey('trace', $exceptions[0]);

        self::assertSame(\LogicException::class, $exceptions[1]['class']);
        self::assertSame('logic exception', $exceptions[1]['message']);
        self::assertSame(2, $exceptions[1]['code']);
        self::assertArrayHasKey('file', $exceptions[1]);
        self::assertArrayHasKey('line', $exceptions[1]);
        self::assertArrayHasKey('trace', $exceptions[1]);
    }
}
