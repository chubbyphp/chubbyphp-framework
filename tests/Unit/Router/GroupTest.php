<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Router\Group
 *
 * @internal
 */
final class GroupTest extends TestCase
{
    public function testMinimal(): void
    {
        $group = Group::create('');

        self::assertSame([], $group->getRoutes());
    }

    public function testMaximal(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MiddlewareInterface $middleware1 */
        $middleware1 = $builder->create(MiddlewareInterface::class, []);

        /** @var MiddlewareInterface $middleware2 */
        $middleware2 = $builder->create(MiddlewareInterface::class, []);

        /** @var MiddlewareInterface $middleware3 */
        $middleware3 = $builder->create(MiddlewareInterface::class, []);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        $group = Group::create('/{id}', [
            Route::get('/{slug}', 'element_read', $handler, [$middleware2], ['tokens' => ['slug' => '[a-z]+']]),
            Group::create('/{slug}', [
                Route::get('/{key}', 'another_route', $handler, [$middleware3], ['tokens' => ['key' => '[a-z]+']]),
            ], [$middleware2], ['tokens' => ['slug' => '[a-z]+']]),
            Route::get(
                '/{slug}/{key}/{subKey}',
                'yet_another_route',
                $handler,
                [$middleware2],
                ['tokens' => ['slug' => '[a-z]+', 'key' => '[a-z]+', 'subKey' => '[a-z]+']]
            ),
        ], [$middleware1], ['tokens' => ['id' => '\d+']]);

        $routes = $group->getRoutes();

        self::assertCount(3, $routes);

        /** @var RouteInterface $route1 */
        $route1 = $routes[0];

        self::assertSame('element_read', $route1->getName());
        self::assertSame('GET', $route1->getMethod());
        self::assertSame('/{id}/{slug}', $route1->getPath());
        self::assertSame(['tokens' => ['id' => '\d+', 'slug' => '[a-z]+']], $route1->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route1->getMiddlewares());
        self::assertSame($handler, $route1->getRequestHandler());

        /** @var RouteInterface $route2 */
        $route2 = $routes[1];

        self::assertSame('another_route', $route2->getName());
        self::assertSame('GET', $route2->getMethod());
        self::assertSame('/{id}/{slug}/{key}', $route2->getPath());
        self::assertSame(
            ['tokens' => ['id' => '\d+', 'slug' => '[a-z]+', 'key' => '[a-z]+']],
            $route2->getPathOptions()
        );
        self::assertSame([$middleware1, $middleware2, $middleware3], $route2->getMiddlewares());
        self::assertSame($handler, $route2->getRequestHandler());

        /** @var RouteInterface $route3 */
        $route3 = $routes[2];

        self::assertSame('yet_another_route', $route3->getName());
        self::assertSame('GET', $route3->getMethod());
        self::assertSame('/{id}/{slug}/{key}/{subKey}', $route3->getPath());
        self::assertSame(
            ['tokens' => ['id' => '\d+', 'slug' => '[a-z]+', 'key' => '[a-z]+', 'subKey' => '[a-z]+']],
            $route3->getPathOptions()
        );
        self::assertSame([$middleware1, $middleware2], $route3->getMiddlewares());
        self::assertSame($handler, $route3->getRequestHandler());
    }
}
