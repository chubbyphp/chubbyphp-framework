<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RoutesByName;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\RoutesByName
 *
 * @internal
 */
final class RoutesByNameTest extends TestCase
{
    public function testWithWrongType(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\Router\RoutesByName::__construct() expects parameter 1'
                .' at index 0 to be Chubbyphp\Framework\Router\RouteInterface, stdClass given'
        );

        $route = new \stdClass();

        new RoutesByName([$route]);
    }

    public function testGetRoutes(): void
    {
        $builder = new MockObjectBuilder();

        $route1 = $builder->create(RouteInterface::class, [
            new WithReturn('getName', [], 'name1'),
        ]);

        $route2 = $builder->create(RouteInterface::class, [
            new WithReturn('getName', [], 'name2'),
        ]);

        $routes = new RoutesByName([$route1, $route2]);

        self::assertSame(['name1' => $route1, 'name2' => $route2], $routes->getRoutesByName());
    }

    public function testWithoutRoutes(): void
    {
        $routes = new RoutesByName([]);

        self::assertSame([], $routes->getRoutesByName());
    }
}
