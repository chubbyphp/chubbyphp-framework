<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use Fig\Http\Message\RequestMethodInterface as RequestMethod;
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
    use MockByCallsTrait;

    public function testMinimal(): void
    {
        $group = Group::create('');

        self::assertSame([], $group->getRoutes());
    }

    public function testWithInvalidChildren(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\Router\Group::addChild() expects parameter 1 to be'
                .' Chubbyphp\Framework\Router\GroupInterface|Chubbyphp\Framework\Router\RouteInterface, stdClass given'
        );

        Group::create('', [new \stdClass()]);
    }

    public function testMaximal(): void
    {
        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware3 */
        $middleware3 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

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

        /** @var RouteInterface */
        $route1 = $routes[0];

        self::assertSame('element_read', $route1->getName());
        self::assertSame(RequestMethod::METHOD_GET, $route1->getMethod());
        self::assertSame('/{id}/{slug}', $route1->getPath());
        self::assertSame(['tokens' => ['id' => '\d+', 'slug' => '[a-z]+']], $route1->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route1->getMiddlewares());
        self::assertSame($handler, $route1->getRequestHandler());

        /** @var RouteInterface */
        $route2 = $routes[1];

        self::assertSame('another_route', $route2->getName());
        self::assertSame(RequestMethod::METHOD_GET, $route2->getMethod());
        self::assertSame('/{id}/{slug}/{key}', $route2->getPath());
        self::assertSame(
            ['tokens' => ['id' => '\d+', 'slug' => '[a-z]+', 'key' => '[a-z]+']],
            $route2->getPathOptions()
        );
        self::assertSame([$middleware1, $middleware2, $middleware3], $route2->getMiddlewares());
        self::assertSame($handler, $route2->getRequestHandler());

        /** @var RouteInterface */
        $route3 = $routes[2];

        self::assertSame('yet_another_route', $route3->getName());
        self::assertSame(RequestMethod::METHOD_GET, $route3->getMethod());
        self::assertSame('/{id}/{slug}/{key}/{subKey}', $route3->getPath());
        self::assertSame(
            ['tokens' => ['id' => '\d+', 'slug' => '[a-z]+', 'key' => '[a-z]+', 'subKey' => '[a-z]+']],
            $route3->getPathOptions()
        );
        self::assertSame([$middleware1, $middleware2], $route3->getMiddlewares());
        self::assertSame($handler, $route3->getRequestHandler());
    }

    public function testMaximalDeprecated(): void
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
                ->middlewares([$middleware2])
                ->route(Route::get('/{key}', 'another_route', $handler)
                    ->pathOptions(['tokens' => ['key' => '[a-z]+']])
                    ->middleware($middleware3)
                )
            )
            ->route(Route::get('/{slug}/{key}/{subKey}', 'yet_another_route', $handler)
                ->pathOptions(['tokens' => ['slug' => '[a-z]+', 'key' => '[a-z]+', 'subKey' => '[a-z]+']])
                ->middleware($middleware2)
            )
        ;

        $routes = $group->getRoutes();

        self::assertCount(3, $routes);

        /** @var RouteInterface */
        $route1 = $routes[0];

        self::assertSame('element_read', $route1->getName());
        self::assertSame(RequestMethod::METHOD_GET, $route1->getMethod());
        self::assertSame('/{id}/{slug}', $route1->getPath());
        self::assertSame(['tokens' => ['id' => '\d+', 'slug' => '[a-z]+']], $route1->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route1->getMiddlewares());
        self::assertSame($handler, $route1->getRequestHandler());

        /** @var RouteInterface */
        $route2 = $routes[1];

        self::assertSame('another_route', $route2->getName());
        self::assertSame(RequestMethod::METHOD_GET, $route2->getMethod());
        self::assertSame('/{id}/{slug}/{key}', $route2->getPath());
        self::assertSame(
            ['tokens' => ['id' => '\d+', 'slug' => '[a-z]+', 'key' => '[a-z]+']],
            $route2->getPathOptions()
        );
        self::assertSame([$middleware1, $middleware2, $middleware3], $route2->getMiddlewares());
        self::assertSame($handler, $route2->getRequestHandler());

        /** @var RouteInterface */
        $route3 = $routes[2];

        self::assertSame('yet_another_route', $route3->getName());
        self::assertSame(RequestMethod::METHOD_GET, $route3->getMethod());
        self::assertSame('/{id}/{slug}/{key}/{subKey}', $route3->getPath());
        self::assertSame(
            ['tokens' => ['id' => '\d+', 'slug' => '[a-z]+', 'key' => '[a-z]+', 'subKey' => '[a-z]+']],
            $route3->getPathOptions()
        );
        self::assertSame([$middleware1, $middleware2], $route3->getMiddlewares());
        self::assertSame($handler, $route3->getRequestHandler());
    }

    public function testWithDeprecatedPathOptionsMethod(): void
    {
        error_clear_last();

        Group::create('')->pathOptions(['tokens' => ['id' => '\d+']]);

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            'Use "$pathOptions" parameter instead of "Chubbyphp\Framework\Router\Group::pathOptions()"',
            $error['message']
        );
    }

    public function testWithDeprecatedMiddlewaresMethod(): void
    {
        /** @var MiddlewareInterface|MockObject $middleware */
        $middleware = $this->getMockByCalls(MiddlewareInterface::class);

        error_clear_last();

        Group::create('')->middlewares([$middleware]);

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            'Use "$middlewares" parameter instead of "Chubbyphp\Framework\Router\Group::middlewares()"',
            $error['message']
        );
    }

    public function testWithDeprecatedMiddlewareMethod(): void
    {
        /** @var MiddlewareInterface|MockObject $middleware */
        $middleware = $this->getMockByCalls(MiddlewareInterface::class);

        error_clear_last();

        Group::create('')->middleware($middleware);

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            'Use "$middlewares" parameter instead of "Chubbyphp\Framework\Router\Group::middleware()"',
            $error['message']
        );
    }
}
