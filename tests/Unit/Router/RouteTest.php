<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Mock\MockByCallsTrait;
use Fig\Http\Message\RequestMethodInterface as RequestMethod;
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

        $route = Route::create(RequestMethod::METHOD_GET, '/{id}', 'read', $handler);

        self::assertSame('read', $route->getName());
        self::assertSame(RequestMethod::METHOD_GET, $route->getMethod());
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

        $route = Route::create(
            RequestMethod::METHOD_GET,
            '/{id}',
            'read',
            $handler,
            [$middleware1, $middleware2],
            ['tokens' => ['id' => '\d+']]
        );

        self::assertSame('read', $route->getName());
        self::assertSame(RequestMethod::METHOD_GET, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testDeleteMinimal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::delete('/{id}', 'delete', $handler);

        self::assertSame('delete', $route->getName());
        self::assertSame(RequestMethod::METHOD_DELETE, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testDeleteMaximal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::delete('/{id}', 'delete', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('delete', $route->getName());
        self::assertSame(RequestMethod::METHOD_DELETE, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testGetMinimal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::get('/{id}', 'read', $handler);

        self::assertSame('read', $route->getName());
        self::assertSame(RequestMethod::METHOD_GET, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testGetMaximal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::get('/{id}', 'get', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('get', $route->getName());
        self::assertSame(RequestMethod::METHOD_GET, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testHeadMinimal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::head('/{id}', 'read_header', $handler);

        self::assertSame('read_header', $route->getName());
        self::assertSame(RequestMethod::METHOD_HEAD, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testHeadMaximal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::head('/{id}', 'head', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('head', $route->getName());
        self::assertSame(RequestMethod::METHOD_HEAD, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testOptionsMinimal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::options('/{id}', 'options', $handler);

        self::assertSame('options', $route->getName());
        self::assertSame(RequestMethod::METHOD_OPTIONS, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testOptionsMaximal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::options('/{id}', 'options', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('options', $route->getName());
        self::assertSame(RequestMethod::METHOD_OPTIONS, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPatchMinimal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::patch('/{id}', 'update', $handler);

        self::assertSame('update', $route->getName());
        self::assertSame(RequestMethod::METHOD_PATCH, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPatchMaximal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::patch('/{id}', 'patch', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('patch', $route->getName());
        self::assertSame(RequestMethod::METHOD_PATCH, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPostMinimal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::post('/{id}', 'create', $handler);

        self::assertSame('create', $route->getName());
        self::assertSame(RequestMethod::METHOD_POST, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPostMaximal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::post('/{id}', 'post', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('post', $route->getName());
        self::assertSame(RequestMethod::METHOD_POST, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPutMinimal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::put('/{id}', 'replace', $handler);

        self::assertSame('replace', $route->getName());
        self::assertSame(RequestMethod::METHOD_PUT, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame([], $route->getPathOptions());
        self::assertSame([], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testPutMaximal(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::put('/{id}', 'put', $handler, [$middleware1, $middleware2], ['tokens' => ['id' => '\d+']]);

        self::assertSame('put', $route->getName());
        self::assertSame(RequestMethod::METHOD_PUT, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testWithAttributes(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $route = Route::create(RequestMethod::METHOD_GET, '/{id}', 'read', $handler);

        $routeClone = $route->withAttributes(['id' => 5]);

        self::assertNotSame($route, $routeClone);

        self::assertSame(['id' => 5], $routeClone->getAttributes());
    }

    public function testMaximalDeprecated(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class);

        $route = Route::create(
            RequestMethod::METHOD_GET,
            '/{id}',
            'read',
            $handler
        )
            ->middlewares([$middleware1])
            ->middleware($middleware2)
            ->pathOptions(['tokens' => ['id' => '\d+']])
        ;

        self::assertSame('read', $route->getName());
        self::assertSame(RequestMethod::METHOD_GET, $route->getMethod());
        self::assertSame('/{id}', $route->getPath());
        self::assertSame(['tokens' => ['id' => '\d+']], $route->getPathOptions());
        self::assertSame([$middleware1, $middleware2], $route->getMiddlewares());
        self::assertSame($handler, $route->getRequestHandler());
        self::assertSame([], $route->getAttributes());
    }

    public function testWithDeprecatedPathOptionsMethod(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        error_clear_last();

        Route::create(RequestMethod::METHOD_GET, '/{id}', 'read', $handler)
            ->pathOptions(['tokens' => ['id' => '\d+']])
        ;

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            'Use "$pathOptions" parameter instead of "Chubbyphp\Framework\Router\Route::pathOptions()"',
            $error['message']
        );
    }

    public function testWithDeprecatedMiddlewaresMethod(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware */
        $middleware = $this->getMockByCalls(MiddlewareInterface::class);

        error_clear_last();

        Route::create(RequestMethod::METHOD_GET, '/{id}', 'read', $handler)
            ->middlewares([$middleware])
        ;

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            'Use "$middlewares" parameter instead of "Chubbyphp\Framework\Router\Route::middlewares()"',
            $error['message']
        );
    }

    public function testWithDeprecatedMiddlewareMethod(): void
    {
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware */
        $middleware = $this->getMockByCalls(MiddlewareInterface::class);

        error_clear_last();

        Route::create(RequestMethod::METHOD_GET, '/{id}', 'read', $handler)
            ->middleware($middleware)
        ;

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            'Use "$middlewares" parameter instead of "Chubbyphp\Framework\Router\Route::middleware()"',
            $error['message']
        );
    }
}
