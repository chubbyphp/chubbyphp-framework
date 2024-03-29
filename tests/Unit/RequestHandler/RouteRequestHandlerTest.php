<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\RequestHandler;

use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\RequestHandler\RouteRequestHandler;
use Chubbyphp\Framework\Router\Exceptions\MissingRouteAttributeOnRequestException;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\RequestHandler\RouteRequestHandler
 *
 * @internal
 */
final class RouteRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testHandleWithoutRoute(): void
    {
        $this->expectException(MissingRouteAttributeOnRequestException::class);
        $this->expectExceptionMessage(
            'Request attribute "route" missing or wrong type "null", please add the'
            .' "Chubbyphp\Framework\Middleware\RouteMatcherMiddleware" middleware'
        );

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('route', null)->willReturn(null),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
        $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class);

        $requestHandler = new RouteRequestHandler($middlewareDispatcher);

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testHandleWithRoute(): void
    {
        /** @var MiddlewareInterface|MockObject $middleware */
        $middleware = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MockObject|RouteInterface $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getMiddlewares')->with()->willReturn([$middleware]),
            Call::create('getRequestHandler')->with()->willReturn($requestHandler),
        ]);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('route', null)->willReturn($route),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
        $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
            Call::create('dispatch')->with([$middleware], $requestHandler, $request)->willReturn($response),
        ]);

        $requestHandler = new RouteRequestHandler($middlewareDispatcher);

        self::assertSame($response, $requestHandler->handle($request));
    }
}
