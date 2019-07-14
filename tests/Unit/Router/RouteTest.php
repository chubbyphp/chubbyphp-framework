<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Router\Route
 *
 * @internal
 */
final class RouteTest extends TestCase
{
    use MockByCallsTrait;

    public function testMinimal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::create(RouteInterface::GET, '/{id}', 'element_read', $handler);

        self::assertSame('element_read', $route->getName());
        self::assertSame(RouteInterface::GET, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testMaximal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::create(RouteInterface::GET, '/{id}', 'element_read', $handler)
            ->pathOptions(['tokens' => ['id' => '\d+']])
            ->middlewares([$middleware1])
            ->middleware($middleware2)
        ;

        self::assertSame('element_read', $route->getName());
        self::assertSame(RouteInterface::GET, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testDelete(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::delete('/{id}', 'element_delete', $handler);

        self::assertSame('element_delete', $route->getName());
        self::assertSame(RouteInterface::DELETE, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testGet(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::get('/{id}', 'element_read', $handler);

        self::assertSame('element_read', $route->getName());
        self::assertSame(RouteInterface::GET, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testHead(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::head('/{id}', 'element_read_header', $handler);

        self::assertSame('element_read_header', $route->getName());
        self::assertSame(RouteInterface::HEAD, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testOptions(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::options('/{id}', 'element_options', $handler);

        self::assertSame('element_options', $route->getName());
        self::assertSame(RouteInterface::OPTIONS, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPatch(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::patch('/{id}', 'element_update', $handler);

        self::assertSame('element_update', $route->getName());
        self::assertSame(RouteInterface::PATCH, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPost(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::post('/{id}', 'element_create', $handler);

        self::assertSame('element_create', $route->getName());
        self::assertSame(RouteInterface::POST, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPut(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::put('/{id}', 'element_replace', $handler);

        self::assertSame('element_replace', $route->getName());
        self::assertSame(RouteInterface::PUT, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testWithAttributes(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::create(RouteInterface::GET, '/{id}', 'element_read', $handler);

        $routeClone = $route->withAttributes(['id' => 5]);

        self::assertNotSame($route, $routeClone);

        self::assertSame(['id' => 5], $routeClone->getAttributes());
    }
}
