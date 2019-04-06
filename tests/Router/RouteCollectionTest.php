<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Router;

use Chubbyphp\Framework\Router\RouteCollection;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Router\RouteCollection
 */
final class RouteCollectionTest extends TestCase
{
    use MockByCallsTrait;

    public function testGroupRouteAndEnd(): void
    {
        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

        $routeCollection = (new RouteCollection())
            ->route('/', RouteInterface::GET, 'index', $requestHandler)
            ->group('/api', [$middleware1])
                ->group('/pet')
                    ->route('', RouteInterface::GET, 'pet_list', $requestHandler, [$middleware2])
                    ->route('', RouteInterface::POST, 'pet_create', $requestHandler)
                    ->route('/{id}', RouteInterface::GET, 'pet_read', $requestHandler)
                    ->route('/{id}', RouteInterface::PUT, 'pet_update', $requestHandler)
                    ->route('/{id}', RouteInterface::DELETE, 'pet_delete', $requestHandler)
                ->end()
            ->end();

        $routes = $routeCollection->getRoutes();

        self::assertCount(6, $routes);

        /** @var RouteInterface $route1 */
        $route1 = $routes['index'];

        self::assertSame('/', $route1->getPath());
        self::assertSame(RouteInterface::GET, $route1->getMethod());
        self::assertSame('index', $route1->getName());
        self::assertSame($requestHandler, $route1->getRequestHandler());
        self::assertSame([], $route1->getMiddlewares());
        self::assertSame([], $route1->getAttributes());

        /** @var RouteInterface $route2 */
        $route2 = $routes['pet_list'];

        self::assertSame('/api/pet', $route2->getPath());
        self::assertSame(RouteInterface::GET, $route2->getMethod());
        self::assertSame('pet_list', $route2->getName());
        self::assertSame($requestHandler, $route2->getRequestHandler());
        self::assertSame([$middleware1, $middleware2], $route2->getMiddlewares());
        self::assertSame([], $route2->getAttributes());

        /** @var RouteInterface $route3 */
        $route3 = $routes['pet_create'];

        self::assertSame('/api/pet', $route3->getPath());
        self::assertSame(RouteInterface::POST, $route3->getMethod());
        self::assertSame('pet_create', $route3->getName());
        self::assertSame($requestHandler, $route3->getRequestHandler());
        self::assertSame([$middleware1], $route3->getMiddlewares());
        self::assertSame([], $route3->getAttributes());

        /** @var RouteInterface $route4 */
        $route4 = $routes['pet_read'];

        self::assertSame('/api/pet/{id}', $route4->getPath());
        self::assertSame(RouteInterface::GET, $route4->getMethod());
        self::assertSame('pet_read', $route4->getName());
        self::assertSame($requestHandler, $route4->getRequestHandler());
        self::assertSame([$middleware1], $route4->getMiddlewares());
        self::assertSame([], $route4->getAttributes());

        /** @var RouteInterface $route5 */
        $route5 = $routes['pet_update'];

        self::assertSame('/api/pet/{id}', $route5->getPath());
        self::assertSame(RouteInterface::PUT, $route5->getMethod());
        self::assertSame('pet_update', $route5->getName());
        self::assertSame($requestHandler, $route5->getRequestHandler());
        self::assertSame([$middleware1], $route5->getMiddlewares());
        self::assertSame([], $route5->getAttributes());

        /** @var RouteInterface $route6 */
        $route6 = $routes['pet_delete'];

        self::assertSame('/api/pet/{id}', $route6->getPath());
        self::assertSame(RouteInterface::DELETE, $route6->getMethod());
        self::assertSame('pet_delete', $route6->getName());
        self::assertSame($requestHandler, $route6->getRequestHandler());
        self::assertSame([$middleware1], $route6->getMiddlewares());
        self::assertSame([], $route6->getAttributes());
    }
}
