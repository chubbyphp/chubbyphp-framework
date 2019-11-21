<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router\Exceptions;

use Chubbyphp\Framework\Router\Exceptions\MissingAttributeForPathGenerationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\Exceptions\MissingAttributeForPathGenerationException
 *
 * @internal
 */
final class MissingAttributeForPathGenerationExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf(
                'Call to private %s::__construct() from context \'%s\'',
                MissingAttributeForPathGenerationException::class,
                self::class
            )
        );

        new MissingAttributeForPathGenerationException('test', 0);
    }

    public function testCreate(): void
    {
        $exception = MissingAttributeForPathGenerationException::create('name', 'attribute');

        self::assertSame(
            'Missing attribute "attribute" while path generation for route: "name"',
            $exception->getMessage()
        );
        self::assertSame(3, $exception->getCode());
        self::assertSame('name', $exception->getName());
        self::assertSame('attribute', $exception->getAttribute());
    }
}
