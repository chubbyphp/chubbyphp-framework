<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit;

use Chubbyphp\Framework\Collection;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Collection
 *
 * @internal
 */
final class CollectionTest extends TestCase
{
    public function testEmpty(): void
    {
        $items = [];

        self::assertSame($items, (new Collection($items, [\stdClass::class]))->toArray());
    }

    public function testValidWithOneType(): void
    {
        $items = [new \stdClass()];

        self::assertSame($items, (new Collection($items, [\stdClass::class]))->toArray());
    }

    public function testInValidWithOneType(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\Collection::__construct() expects parameter 1 at index 1 to be'
                .' stdClass, DateTimeImmutable given'
        );

        new Collection([new \stdClass(), new \DateTimeImmutable()], [\stdClass::class]);
    }

    public function testValidWithMultipleTypes(): void
    {
        $items = [new \stdClass(), new \DateTimeImmutable(), new \Exception()];

        self::assertSame(
            $items,
            (new Collection($items, [\stdClass::class, \DateTimeImmutable::class, \Exception::class]))->toArray()
        );
    }

    public function testInValidWithMultipleTypes(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\Collection::__construct() expects parameter 1 at index 1 to be'
                .' stdClass|DateTimeImmutable|Exception, Error given'
        );

        new Collection(
            [new \stdClass(), new \Error(), new \DateTimeImmutable()],
            [\stdClass::class, \DateTimeImmutable::class, \Exception::class]
        );
    }
}
