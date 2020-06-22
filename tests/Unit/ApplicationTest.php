<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Emitter\EmitterInterface;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouterException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Application
 *
 * @internal
 */
final class ApplicationTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var MiddlewareInterface|MockObject $routeIndependMiddleware */
        $routeIndependMiddleware = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware */
        $middleware = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getMiddlewares')->with()->willReturn([$middleware]),
            Call::create('getRequestHandler')->with()->willReturn($handler),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('route', null)->willReturn($route),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
        $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
            Call::create('dispatch')
                ->willReturnCallback(
                    function (
                        array $middlewares,
                        CallbackRequestHandler $requestHandler,
                        ServerRequestInterface $request
                    ) use ($routeIndependMiddleware) {
                        self::assertSame([$routeIndependMiddleware], $middlewares);

                        return $requestHandler->handle($request);
                    }
                ),
            Call::create('dispatch')->with([$middleware], $handler, $request)->willReturn($response),
        ]);

        $application = new Application([
            $routeIndependMiddleware,
        ], $middlewareDispatcher);

        self::assertSame($response, $application($request));
    }

    public function testHandle(): void
    {
        /** @var MiddlewareInterface|MockObject $routeIndependMiddleware */
        $routeIndependMiddleware = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware */
        $middleware = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getMiddlewares')->with()->willReturn([$middleware]),
            Call::create('getRequestHandler')->with()->willReturn($handler),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('route', null)->willReturn($route),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
        $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
            Call::create('dispatch')
                ->willReturnCallback(
                    function (
                        array $middlewares,
                        CallbackRequestHandler $requestHandler,
                        ServerRequestInterface $request
                    ) use ($routeIndependMiddleware) {
                        self::assertSame([$routeIndependMiddleware], $middlewares);

                        return $requestHandler->handle($request);
                    }
                ),
            Call::create('dispatch')->with([$middleware], $handler, $request)->willReturn($response),
        ]);

        $application = new Application([
            $routeIndependMiddleware,
        ], $middlewareDispatcher);

        self::assertSame($response, $application->handle($request));
    }

    public function testHandleWithMissingRouteAttribute(): void
    {
        $this->expectException(RouterException::class);
        $this->expectExceptionMessage(
            'Request attribute "route" missing or wrong type "stdClass",'
                .' please add the "Chubbyphp\Framework\Middleware\RouterMiddleware" middleware'
        );

        /** @var MiddlewareInterface|MockObject $routeIndependMiddleware */
        $routeIndependMiddleware = $this->getMockByCalls(MiddlewareInterface::class);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('route', null)->willReturn(new \stdClass()),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
        $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
            Call::create('dispatch')
                ->willReturnCallback(
                    function (
                        array $middlewares,
                        CallbackRequestHandler $requestHandler,
                        ServerRequestInterface $request
                    ) use ($routeIndependMiddleware) {
                        self::assertSame([$routeIndependMiddleware], $middlewares);

                        return $requestHandler->handle($request);
                    }
                ),
        ]);

        $application = new Application([
            $routeIndependMiddleware,
        ], $middlewareDispatcher);

        self::assertSame($response, $application->handle($request));
    }

    public function testEmit(): void
    {
        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var EmitterInterface|MockObject $emitter */
        $emitter = $this->getMockByCalls(EmitterInterface::class, [
            Call::create('emit')->with($response),
        ]);

        $application = new Application([], null, $emitter);
        $application->emit($response);
    }
}
