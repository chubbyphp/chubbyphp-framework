<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\Route;
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
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::create('GET', '/{id}', 'read', $handler);

        self::assertSame('read', $route->getName());
        self::assertSame('GET', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testMaximal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::create(
            'GET',
            '/{id}',
            'read',
            $handler,
            [$middleware1, $middleware2],
            ['tokens' => ['id' => '\d+']]
        );

        self::assertSame('read', $route->getName());
        self::assertSame('GET', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testDeleteMinimal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::delete('/{id}', 'delete', $handler);

        self::assertSame('delete', $route->getName());
        self::assertSame('DELETE', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testDeleteMaximal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::delete('/{id}', 'delete', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('delete', $route->getName());
        self::assertSame('DELETE', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testGetMinimal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::get('/{id}', 'read', $handler);

        self::assertSame('read', $route->getName());
        self::assertSame('GET', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testGetMaximal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::get('/{id}', 'get', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('get', $route->getName());
        self::assertSame('GET', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testHeadMinimal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::head('/{id}', 'read_header', $handler);

        self::assertSame('read_header', $route->getName());
        self::assertSame('HEAD', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testHeadMaximal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::head('/{id}', 'head', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('head', $route->getName());
        self::assertSame('HEAD', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testOptionsMinimal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::options('/{id}', 'options', $handler);

        self::assertSame('options', $route->getName());
        self::assertSame('OPTIONS', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testOptionsMaximal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::options('/{id}', 'options', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('options', $route->getName());
        self::assertSame('OPTIONS', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPatchMinimal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::patch('/{id}', 'update', $handler);

        self::assertSame('update', $route->getName());
        self::assertSame('PATCH', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPatchMaximal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::patch('/{id}', 'patch', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('patch', $route->getName());
        self::assertSame('PATCH', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPostMinimal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::post('/{id}', 'create', $handler);

        self::assertSame('create', $route->getName());
        self::assertSame('POST', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPostMaximal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::post('/{id}', 'post', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('post', $route->getName());
        self::assertSame('POST', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPutMinimal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::put('/{id}', 'replace', $handler);

        self::assertSame('replace', $route->getName());
        self::assertSame('PUT', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPutMaximal(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::put('/{id}', 'put', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('put', $route->getName());
        self::assertSame('PUT', $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testWithAttributes(): void
    {
        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::create('GET', '/{id}', 'read', $handler);

        $routeClone = $route->withAttributes(['id' => 5]);

        self::assertNotSame($route, $routeClone);

        self::assertSame(['id' => 5], $routeClone->getAttributes());
    }
}
