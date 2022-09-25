<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RoutesByName;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\RoutesByName
 *
 * @internal
 */
final class RoutesByNameTest extends TestCase
{
    use MockByCallsTrait;

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
        $route1 = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('name1'),
        ]);

        $route2 = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('name2'),
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
