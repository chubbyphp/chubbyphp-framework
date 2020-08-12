<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router\Exceptions;

use Chubbyphp\Framework\Router\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\Exceptions\NotFoundException
 *
 * @internal
 */
final class NotFoundExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to private');

        new NotFoundException('test', 0);
    }

    public function testCreate(): void
    {
        $exception = NotFoundException::create('/');

        self::assertSame('https://tools.ietf.org/html/rfc7231#section-6.5.4', $exception->getType());
        self::assertSame('Page not found', $exception->getTitle());
        self::assertSame(
            'The page "/" you are looking for could not be found.'
                .' Check the address bar to ensure your URL is spelled correctly.',
            $exception->getMessage()
        );
        self::assertSame(404, $exception->getCode());
    }
}
