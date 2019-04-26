<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Router\Group
 */
final class GroupTest extends TestCase
{
    use MockByCallsTrait;

    public function testMinimal()
    {
        $group = Group::create('');

        self::assertSame([], $group->getRoutes());
    }

    public function testMaximal()
    {
        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware3 */
        $middleware3 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $group = Group::create('/{id}')
            ->pathOptions(['tokens' => ['id' => '\d+']])
            ->middleware($middleware1)
            ->route(Route::get('/{slug}', 'element_read', $handler)
                ->pathOptions(['tokens' => ['slug' => '[a-z]+']])
                ->middleware($middleware2)
            )
            ->group(Group::create('/{slug}')
                ->pathOptions(['tokens' => ['slug' => '[a-z]+']])
                ->middleware($middleware2)
                ->route(Route::get('/{key}', 'another_route', $handler)
                    ->pathOptions(['tokens' => ['key' => '[a-z]+']])
                    ->middleware($middleware3)
                )
            )
        ;

        $routes = $group->getRoutes();

        self::assertCount(2, $routes);

        /** @var RouteInterface */
        $route1 = $routes[0];

        self::assertSame('element_read', $route1->getName());
        self::assertSame(RouteInterface::GET, $route1->getMethod());
        self::assertSame('/{id}/{slug}', $route1->getPath());
        self::assertSame(['tokens' => ['id' => '\d+', 'slug' => '[a-z]+']], $route1->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route1->getMiddlewares());
        self::assertSame($handler, $route1->getRequestHandler());

        /** @var RouteInterface */
        $route2 = $routes[1];

        self::assertSame('another_route', $route2->getName());
        self::assertSame(RouteInterface::GET, $route2->getMethod());
        self::assertSame('/{id}/{slug}/{key}', $route2->getPath());
        self::assertSame(
            ['tokens' => ['id' => '\d+', 'slug' => '[a-z]+', 'key' => '[a-z]+']],
            $route2->getPathOptions()
        );
        self::assertSame([$middleware1, $middleware2, $middleware3], $route2->getMiddlewares());
        self::assertSame($handler, $route2->getRequestHandler());
    }
}
