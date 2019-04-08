<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Router;

use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Router\Route
 */
final class RouteTest extends TestCase
{
    use MockByCallsTrait;

    public function testConstruct(): void
    {
        /** @var MiddlewareInterface|MockObject $middleware */
        $middleware = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = new Route('/', RouteInterface::GET, 'index', $requestHandler, [$middleware]);

        self::assertSame('/', $route->getPattern());
        self::assertSame(RouteInterface::GET, $route->getMethod());
        self::assertSame('index', $route->getName());
        self::assertSame($requestHandler, $route->getRequestHandler());
        self::assertSame([$middleware], $route->getMiddlewares());
        self::assertSame([], $route->getAttributes());
        self::assertSame('/::GET::index', (string) $route);
    }

    public function testWithAttributes(): void
    {
        /** @var MiddlewareInterface|MockObject $middleware */
        $middleware = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = new Route('/{key}', RouteInterface::GET, 'index', $requestHandler, [$middleware]);
        $routeClone = $route->withAttributes(['key' => 'value']);

        self::assertNotSame(spl_object_id($routeClone), spl_object_id($route));

        self::assertSame('/{key}', $routeClone->getPattern());
        self::assertSame(RouteInterface::GET, $routeClone->getMethod());
        self::assertSame('index', $routeClone->getName());
        self::assertSame($requestHandler, $routeClone->getRequestHandler());
        self::assertSame([$middleware], $routeClone->getMiddlewares());
        self::assertSame(['key' => 'value'], $routeClone->getAttributes());
        self::assertSame('/{key}::GET::index', (string) $route);
    }
}
