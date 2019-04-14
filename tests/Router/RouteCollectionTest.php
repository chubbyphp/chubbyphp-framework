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
use Chubbyphp\Framework\Router\RouteCollectionException;

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
            ->group('/api')
                ->group('/pet', [$middleware1])
                    ->get('', 'pet_list', $requestHandler, [$middleware2])
                    ->post('', 'pet_create', $requestHandler)
                    ->get('/{id}', 'pet_read', $requestHandler)
                    ->patch('/{id}', 'pet_update', $requestHandler)
                    ->put('/{id}', 'pet_replace', $requestHandler)
                    ->delete('/{id}', 'pet_delete', $requestHandler)
                ->end()
                ->route('/ping', RouteInterface::GET, 'ping', $requestHandler)
            ->end();

        $routes = $routeCollection->getRoutes();

        self::assertCount(8, $routes);

        /** @var RouteInterface $route1 */
        $route1 = $routes['index'];

        self::assertSame('/', $route1->getPattern());
        self::assertSame(RouteInterface::GET, $route1->getMethod());
        self::assertSame('index', $route1->getName());
        self::assertSame($requestHandler, $route1->getRequestHandler());
        self::assertSame([], $route1->getMiddlewares());
        self::assertSame([], $route1->getAttributes());

        /** @var RouteInterface $route2 */
        $route2 = $routes['pet_list'];

        self::assertSame('/api/pet', $route2->getPattern());
        self::assertSame(RouteInterface::GET, $route2->getMethod());
        self::assertSame('pet_list', $route2->getName());
        self::assertSame($requestHandler, $route2->getRequestHandler());
        self::assertSame([$middleware1, $middleware2], $route2->getMiddlewares());
        self::assertSame([], $route2->getAttributes());

        /** @var RouteInterface $route3 */
        $route3 = $routes['pet_create'];

        self::assertSame('/api/pet', $route3->getPattern());
        self::assertSame(RouteInterface::POST, $route3->getMethod());
        self::assertSame('pet_create', $route3->getName());
        self::assertSame($requestHandler, $route3->getRequestHandler());
        self::assertSame([$middleware1], $route3->getMiddlewares());
        self::assertSame([], $route3->getAttributes());

        /** @var RouteInterface $route4 */
        $route4 = $routes['pet_read'];

        self::assertSame('/api/pet/{id}', $route4->getPattern());
        self::assertSame(RouteInterface::GET, $route4->getMethod());
        self::assertSame('pet_read', $route4->getName());
        self::assertSame($requestHandler, $route4->getRequestHandler());
        self::assertSame([$middleware1], $route4->getMiddlewares());
        self::assertSame([], $route4->getAttributes());

        /** @var RouteInterface $route5 */
        $route5 = $routes['pet_update'];

        self::assertSame('/api/pet/{id}', $route5->getPattern());
        self::assertSame(RouteInterface::PATCH, $route5->getMethod());
        self::assertSame('pet_update', $route5->getName());
        self::assertSame($requestHandler, $route5->getRequestHandler());
        self::assertSame([$middleware1], $route5->getMiddlewares());
        self::assertSame([], $route5->getAttributes());

        /** @var RouteInterface $route6 */
        $route6 = $routes['pet_replace'];

        self::assertSame('/api/pet/{id}', $route6->getPattern());
        self::assertSame(RouteInterface::PUT, $route6->getMethod());
        self::assertSame('pet_replace', $route6->getName());
        self::assertSame($requestHandler, $route6->getRequestHandler());
        self::assertSame([$middleware1], $route6->getMiddlewares());
        self::assertSame([], $route6->getAttributes());

        /** @var RouteInterface $route7 */
        $route7 = $routes['pet_delete'];

        self::assertSame('/api/pet/{id}', $route7->getPattern());
        self::assertSame(RouteInterface::DELETE, $route7->getMethod());
        self::assertSame('pet_delete', $route7->getName());
        self::assertSame($requestHandler, $route7->getRequestHandler());
        self::assertSame([$middleware1], $route7->getMiddlewares());
        self::assertSame([], $route7->getAttributes());

        /** @var RouteInterface $route8 */
        $route8 = $routes['ping'];

        self::assertSame('/api/ping', $route8->getPattern());
        self::assertSame(RouteInterface::GET, $route8->getMethod());
        self::assertSame('ping', $route8->getName());
        self::assertSame($requestHandler, $route8->getRequestHandler());
        self::assertSame([], $route8->getMiddlewares());
        self::assertSame([], $route8->getAttributes());

        $expectedString = <<<'EOT'
/::GET::index
/api/pet::GET::pet_list
/api/pet::POST::pet_create
/api/pet/{id}::GET::pet_read
/api/pet/{id}::PATCH::pet_update
/api/pet/{id}::PUT::pet_replace
/api/pet/{id}::DELETE::pet_delete
/api/ping::GET::ping
EOT;

        self::assertSame($expectedString, (string) $routeCollection);
    }

    public function testFrozenWithGroup(): void
    {
        $this->expectException(RouteCollectionException::class);
        $this->expectExceptionMessage('The route collection is frozen');
        $this->expectExceptionCode(1);

        $routeCollection = new RouteCollection();
        $routeCollection->getRoutes();
        $routeCollection->group('/api');
    }

    public function testFrozenWithRoute(): void
    {
        $this->expectException(RouteCollectionException::class);
        $this->expectExceptionMessage('The route collection is frozen');
        $this->expectExceptionCode(1);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

        $routeCollection = new RouteCollection();
        $routeCollection->getRoutes();
        $routeCollection->route('', RouteInterface::POST, 'pet_create', $requestHandler);
    }

    public function testFrozenWithEnd(): void
    {
        $this->expectException(RouteCollectionException::class);
        $this->expectExceptionMessage('The route collection is frozen');
        $this->expectExceptionCode(1);

        $routeCollection = new RouteCollection();
        $routeCollection->getRoutes();
        $routeCollection->end();
    }
}
