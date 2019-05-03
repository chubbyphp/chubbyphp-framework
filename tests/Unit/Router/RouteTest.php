<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @covers \Chubbyphp\Framework\Router\Route
 */
final class RouteTest extends TestCase
{
    use MockByCallsTrait;

    public function testMinimal()
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

    public function testMaximal()
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

    public function testDelete()
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

    public function testGet()
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

    public function testHead()
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

    public function testOptions()
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

    public function testPatch()
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

    public function testPost()
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

    public function testPut()
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

    public function testWithAttributes()
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::create(RouteInterface::GET, '/{id}', 'element_read', $handler);

        $routeClone = $route->withAttributes(['id' => 5]);

        self::assertNotSame($route, $routeClone);

        self::assertSame(['id' => 5], $routeClone->getAttributes());
    }
}
