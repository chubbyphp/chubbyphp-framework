<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Router\FastRoute;

use Chubbyphp\Framework\Router\FastRoute\RouteCollection;
use Chubbyphp\Framework\Router\RouteCollectionException;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Router\FastRoute\RouteCollection
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
            ->get('/', 'index', $requestHandler)
            ->options('/{path:.*}', 'cors', $requestHandler)
            ->group('/api')
                ->group('/pet', [$middleware1])
                    ->get('', 'pet_list', $requestHandler, [$middleware2])
                    ->post('', 'pet_create', $requestHandler)
                    ->get('/{id:\d+}', 'pet_read', $requestHandler)
                    ->head('/{id:\d+}', 'pet_read_header', $requestHandler)
                    ->patch('/{id:\d+}', 'pet_update', $requestHandler)
                    ->put('/{id:\d+}', 'pet_replace', $requestHandler)
                    ->delete('/{id:\d+}', 'pet_delete', $requestHandler)
                ->end()
                ->get('/ping', 'ping', $requestHandler)
            ->end();

        $routes = $routeCollection->getRoutes();

        self::assertCount(10, $routes);

        /** @var RouteInterface $route1 */
        $route1 = $routes['index'];

        self::assertSame('/', $route1->getPattern());
        self::assertSame(RouteInterface::GET, $route1->getMethod());
        self::assertSame('index', $route1->getName());
        self::assertSame($requestHandler, $route1->getRequestHandler());
        self::assertSame([], $route1->getMiddlewares());
        self::assertSame([], $route1->getAttributes());

        /** @var RouteInterface $route2 */
        $route2 = $routes['cors'];

        self::assertSame('/{path:.*}', $route2->getPattern());
        self::assertSame(RouteInterface::OPTIONS, $route2->getMethod());
        self::assertSame('cors', $route2->getName());
        self::assertSame($requestHandler, $route2->getRequestHandler());
        self::assertSame([], $route2->getMiddlewares());
        self::assertSame([], $route2->getAttributes());

        /** @var RouteInterface $route3 */
        $route3 = $routes['pet_list'];

        self::assertSame('/api/pet', $route3->getPattern());
        self::assertSame(RouteInterface::GET, $route3->getMethod());
        self::assertSame('pet_list', $route3->getName());
        self::assertSame($requestHandler, $route3->getRequestHandler());
        self::assertSame([$middleware1, $middleware2], $route3->getMiddlewares());
        self::assertSame([], $route3->getAttributes());

        /** @var RouteInterface $route4 */
        $route4 = $routes['pet_create'];

        self::assertSame('/api/pet', $route4->getPattern());
        self::assertSame(RouteInterface::POST, $route4->getMethod());
        self::assertSame('pet_create', $route4->getName());
        self::assertSame($requestHandler, $route4->getRequestHandler());
        self::assertSame([$middleware1], $route4->getMiddlewares());
        self::assertSame([], $route4->getAttributes());

        /** @var RouteInterface $route5 */
        $route5 = $routes['pet_read'];

        self::assertSame('/api/pet/{id:\d+}', $route5->getPattern());
        self::assertSame(RouteInterface::GET, $route5->getMethod());
        self::assertSame('pet_read', $route5->getName());
        self::assertSame($requestHandler, $route5->getRequestHandler());
        self::assertSame([$middleware1], $route5->getMiddlewares());
        self::assertSame([], $route5->getAttributes());

        /** @var RouteInterface $route6 */
        $route6 = $routes['pet_read_header'];

        self::assertSame('/api/pet/{id:\d+}', $route6->getPattern());
        self::assertSame(RouteInterface::HEAD, $route6->getMethod());
        self::assertSame('pet_read_header', $route6->getName());
        self::assertSame($requestHandler, $route6->getRequestHandler());
        self::assertSame([$middleware1], $route6->getMiddlewares());
        self::assertSame([], $route6->getAttributes());

        /** @var RouteInterface $route7 */
        $route7 = $routes['pet_update'];

        self::assertSame('/api/pet/{id:\d+}', $route7->getPattern());
        self::assertSame(RouteInterface::PATCH, $route7->getMethod());
        self::assertSame('pet_update', $route7->getName());
        self::assertSame($requestHandler, $route7->getRequestHandler());
        self::assertSame([$middleware1], $route7->getMiddlewares());
        self::assertSame([], $route7->getAttributes());

        /** @var RouteInterface $route8 */
        $route8 = $routes['pet_replace'];

        self::assertSame('/api/pet/{id:\d+}', $route8->getPattern());
        self::assertSame(RouteInterface::PUT, $route8->getMethod());
        self::assertSame('pet_replace', $route8->getName());
        self::assertSame($requestHandler, $route8->getRequestHandler());
        self::assertSame([$middleware1], $route8->getMiddlewares());
        self::assertSame([], $route8->getAttributes());

        /** @var RouteInterface $route9 */
        $route9 = $routes['pet_delete'];

        self::assertSame('/api/pet/{id:\d+}', $route9->getPattern());
        self::assertSame(RouteInterface::DELETE, $route9->getMethod());
        self::assertSame('pet_delete', $route9->getName());
        self::assertSame($requestHandler, $route9->getRequestHandler());
        self::assertSame([$middleware1], $route9->getMiddlewares());
        self::assertSame([], $route9->getAttributes());

        /** @var RouteInterface $route10 */
        $route10 = $routes['ping'];

        self::assertSame('/api/ping', $route10->getPattern());
        self::assertSame(RouteInterface::GET, $route10->getMethod());
        self::assertSame('ping', $route10->getName());
        self::assertSame($requestHandler, $route10->getRequestHandler());
        self::assertSame([], $route10->getMiddlewares());
        self::assertSame([], $route10->getAttributes());

        $expectedString = <<<'EOT'
/::[]::GET::index
/{path:.*}::[]::OPTIONS::cors
/api/pet::[]::GET::pet_list
/api/pet::[]::POST::pet_create
/api/pet/{id:\d+}::[]::GET::pet_read
/api/pet/{id:\d+}::[]::HEAD::pet_read_header
/api/pet/{id:\d+}::[]::PATCH::pet_update
/api/pet/{id:\d+}::[]::PUT::pet_replace
/api/pet/{id:\d+}::[]::DELETE::pet_delete
/api/ping::[]::GET::ping
EOT;

        self::assertSame($expectedString, (string) $routeCollection);
    }

    public function testWithInvalidMiddlewareInGroup(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\Router\FastRoute\RouteCollection::group expects parameter 1 to be '
                .'Psr\Http\Server\MiddlewareInterface[], stdClass[] given'
        );

        $routeCollection = new RouteCollection();
        $routeCollection->group('/api', [new \stdClass()]);
    }

    public function testWithInvalidMiddlewareInRoute(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\Router\FastRoute\RouteCollection::route expects parameter 1 to be '
                .'Psr\Http\Server\MiddlewareInterface[], stdClass[] given'
        );

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

        $routeCollection = new RouteCollection();
        $routeCollection->get('/pets', 'pets', $requestHandler, [new \stdClass()]);
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
        $routeCollection->post('/pets', 'pet_create', $requestHandler);
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
