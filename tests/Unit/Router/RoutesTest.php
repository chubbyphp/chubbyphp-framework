<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\Routes;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\Routes
 *
 * @internal
 */
final class RoutesTest extends TestCase
{
    use MockByCallsTrait;

    public function testWithWrongType(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s::__construct() expects parameter 1 at index %d to be %s[], %s[] given',
                Routes::class,
                0,
                RouteInterface::class,
                \stdClass::class,
            )
        );

        $route = new \stdClass();

        new Routes([$route]);
    }

    public function testGetRoutes(): void
    {
        $route1 = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('name1'),
        ]);

        $route2 = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('name2'),
        ]);

        $routes = new Routes([$route1, $route2]);

        self::assertSame(['name1' => $route1, 'name2' => $route2], $routes->getRoutesByName());
    }
}
