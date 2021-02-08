<?php

declare(strict_types=1);

namespace Chubbyphp\Framework
{
    final class TestHeader
    {
        /**
         * @var array
         */
        private static $headers = [];

        /**
         * @param int $http_response_code
         */
        public static function add(string $header, bool $replace = true, int $http_response_code = null): void
        {
            self::$headers[] = [
                'header' => $header,
                'replace' => $replace,
                'http_response_code' => $http_response_code,
            ];
        }

        public static function all(): array
        {
            return self::$headers;
        }

        public static function reset(): void
        {
            self::$headers = [];
        }
    }

    /**
     * @param int $http_response_code
     */
    function header(string $header, bool $replace = true, int $http_response_code = null): void
    {
        TestHeader::add($header, $replace, $http_response_code);
    }
}

namespace Chubbyphp\Tests\Framework\Unit
{
    use Chubbyphp\Framework\Application;
    use Chubbyphp\Framework\ExceptionHandlerInterface;
    use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
    use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
    use Chubbyphp\Framework\Router\RouteInterface;
    use Chubbyphp\Framework\Router\RouterException;
    use Chubbyphp\Framework\Router\RouterInterface;
    use Chubbyphp\Framework\TestHeader;
    use Chubbyphp\Mock\Call;
    use Chubbyphp\Mock\MockByCallsTrait;
    use PHPUnit\Framework\MockObject\MockObject;
    use PHPUnit\Framework\TestCase;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\StreamInterface;
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

        public function testHandle(): void
        {
            /** @var MiddlewareInterface|MockObject $middleware */
            $middleware = $this->getMockByCalls(MiddlewareInterface::class);

            /** @var RequestHandlerInterface|MockObject $handler */
            $handler = $this->getMockByCalls(RequestHandlerInterface::class);

            /** @var RouteInterface|MockObject $route */
            $route = $this->getMockByCalls(RouteInterface::class, [
                Call::create('getAttributes')->with()->willReturn(['key' => 'value']),
                Call::create('getMiddlewares')->with()->willReturn([$middleware]),
                Call::create('getRequestHandler')->with()->willReturn($handler),
            ]);

            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class, [
                // @deprecated remove this line in v2
                Call::create('withAttribute')->with('route', $route)->willReturnSelf(),
                Call::create('withAttribute')->with('key', 'value')->willReturnSelf(),
            ]);

            /** @var ResponseInterface|MockObject $response */
            $response = $this->getMockByCalls(ResponseInterface::class);

            /** @var RouterInterface|MockObject $router */
            $router = $this->getMockByCalls(RouterInterface::class, [
                Call::create('match')->with($request)->willReturn($route),
            ]);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
                Call::create('dispatch')->with([$middleware], $handler, $request)->willReturn($response),
            ]);

            /** @var ExceptionHandlerInterface|MockObject $exceptionHandler */
            $exceptionHandler = $this->getMockByCalls(ExceptionHandlerInterface::class);

            $application = new Application(
                $router,
                $middlewareDispatcher,
                $exceptionHandler
            );

            self::assertSame($response, $application->handle($request));
        }

        public function testHandleWithMiddlewares(): void
        {
            /** @var MiddlewareInterface|MockObject $routeIndependMiddleware */
            $routeIndependMiddleware = $this->getMockByCalls(MiddlewareInterface::class);

            /** @var MiddlewareInterface|MockObject $middleware */
            $middleware = $this->getMockByCalls(MiddlewareInterface::class);

            /** @var RequestHandlerInterface|MockObject $handler */
            $handler = $this->getMockByCalls(RequestHandlerInterface::class);

            /** @var RouteInterface|MockObject $route */
            $route = $this->getMockByCalls(RouteInterface::class, [
                Call::create('getAttributes')->with()->willReturn(['key' => 'value']),
                Call::create('getMiddlewares')->with()->willReturn([$middleware]),
                Call::create('getRequestHandler')->with()->willReturn($handler),
            ]);

            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class, [
                // @deprecated remove this line in v2
                Call::create('withAttribute')->with('route', $route)->willReturnSelf(),
                Call::create('withAttribute')->with('key', 'value')->willReturnSelf(),
            ]);

            /** @var ResponseInterface|MockObject $response */
            $response = $this->getMockByCalls(ResponseInterface::class);

            /** @var RouterInterface|MockObject $router */
            $router = $this->getMockByCalls(RouterInterface::class, [
                Call::create('match')->with($request)->willReturn($route),
            ]);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
                Call::create('dispatch')
                    ->willReturnCallback(
                        static function (
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

            /** @var ExceptionHandlerInterface|MockObject $exceptionHandler */
            $exceptionHandler = $this->getMockByCalls(ExceptionHandlerInterface::class);

            $application = new Application(
                $router,
                $middlewareDispatcher,
                $exceptionHandler,
                [$routeIndependMiddleware]
            );

            self::assertSame($response, $application->handle($request));
        }

        public function testHandleRouterException(): void
        {
            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class);

            /** @var ResponseInterface|MockObject $response */
            $response = $this->getMockByCalls(ResponseInterface::class);

            $routerException = RouterException::createForNotFound('/');

            /** @var RouterInterface|MockObject $router */
            $router = $this->getMockByCalls(RouterInterface::class, [
                Call::create('match')->with($request)->willThrowException($routerException),
            ]);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class);

            /** @var ExceptionHandlerInterface|MockObject $exceptionHandler */
            $exceptionHandler = $this->getMockByCalls(ExceptionHandlerInterface::class, [
                Call::create('createRouterExceptionResponse')->with($request, $routerException)->willReturn($response),
            ]);

            $application = new Application(
                $router,
                $middlewareDispatcher,
                $exceptionHandler
            );

            self::assertSame($response, $application->handle($request));
        }

        public function testHandleRouterExceptionWithMiddlewares(): void
        {
            /** @var MiddlewareInterface|MockObject $routeIndependMiddleware */
            $routeIndependMiddleware = $this->getMockByCalls(MiddlewareInterface::class);

            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class);

            /** @var ResponseInterface|MockObject $response */
            $response = $this->getMockByCalls(ResponseInterface::class);

            $routerException = RouterException::createForNotFound('/');

            /** @var RouterInterface|MockObject $router */
            $router = $this->getMockByCalls(RouterInterface::class, [
                Call::create('match')->with($request)->willThrowException($routerException),
            ]);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
                Call::create('dispatch')
                    ->willReturnCallback(
                        static function (
                            array $middlewares,
                            CallbackRequestHandler $requestHandler,
                            ServerRequestInterface $request
                        ) use ($routeIndependMiddleware) {
                            self::assertSame([$routeIndependMiddleware], $middlewares);

                            return $requestHandler->handle($request);
                        }
                    ),
            ]);

            /** @var ExceptionHandlerInterface|MockObject $exceptionHandler */
            $exceptionHandler = $this->getMockByCalls(ExceptionHandlerInterface::class, [
                Call::create('createRouterExceptionResponse')->with($request, $routerException)->willReturn($response),
            ]);

            $application = new Application(
                $router,
                $middlewareDispatcher,
                $exceptionHandler,
                [$routeIndependMiddleware]
            );

            self::assertSame($response, $application->handle($request));
        }

        public function testHandleThrowable(): void
        {
            /** @var MiddlewareInterface|MockObject $middleware */
            $middleware = $this->getMockByCalls(MiddlewareInterface::class);

            /** @var RequestHandlerInterface|MockObject $handler */
            $handler = $this->getMockByCalls(RequestHandlerInterface::class);

            /** @var RouteInterface|MockObject $route */
            $route = $this->getMockByCalls(RouteInterface::class, [
                Call::create('getAttributes')->with()->willReturn(['key' => 'value']),
                Call::create('getMiddlewares')->with()->willReturn([$middleware]),
                Call::create('getRequestHandler')->with()->willReturn($handler),
            ]);

            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class, [
                // @deprecated remove this line in v2
                Call::create('withAttribute')->with('route', $route)->willReturnSelf(),
                Call::create('withAttribute')->with('key', 'value')->willReturnSelf(),
            ]);

            /** @var ResponseInterface|MockObject $response */
            $response = $this->getMockByCalls(ResponseInterface::class);

            $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

            /** @var RouterInterface|MockObject $router */
            $router = $this->getMockByCalls(RouterInterface::class, [
                Call::create('match')->with($request)->willReturn($route),
            ]);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
                Call::create('dispatch')->with([$middleware], $handler, $request)->willThrowException($exception),
            ]);

            /** @var ExceptionHandlerInterface|MockObject $exceptionHandler */
            $exceptionHandler = $this->getMockByCalls(ExceptionHandlerInterface::class, [
                Call::create('createExceptionResponse')->with($request, $exception)->willReturn($response),
            ]);

            $application = new Application(
                $router,
                $middlewareDispatcher,
                $exceptionHandler
            );

            self::assertSame($response, $application->handle($request));
        }

        public function testHandleThrowableWithMiddlewares(): void
        {
            /** @var MiddlewareInterface|MockObject $routeIndependMiddleware */
            $routeIndependMiddleware = $this->getMockByCalls(MiddlewareInterface::class);

            /** @var MiddlewareInterface|MockObject $middleware */
            $middleware = $this->getMockByCalls(MiddlewareInterface::class);

            /** @var RequestHandlerInterface|MockObject $handler */
            $handler = $this->getMockByCalls(RequestHandlerInterface::class);

            /** @var RouteInterface|MockObject $route */
            $route = $this->getMockByCalls(RouteInterface::class, [
                Call::create('getAttributes')->with()->willReturn(['key' => 'value']),
                Call::create('getMiddlewares')->with()->willReturn([$middleware]),
                Call::create('getRequestHandler')->with()->willReturn($handler),
            ]);

            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class, [
                // @deprecated remove this line in v2
                Call::create('withAttribute')->with('route', $route)->willReturnSelf(),
                Call::create('withAttribute')->with('key', 'value')->willReturnSelf(),
            ]);

            /** @var ResponseInterface|MockObject $response */
            $response = $this->getMockByCalls(ResponseInterface::class);

            $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

            /** @var RouterInterface|MockObject $router */
            $router = $this->getMockByCalls(RouterInterface::class, [
                Call::create('match')->with($request)->willReturn($route),
            ]);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
                Call::create('dispatch')
                    ->willReturnCallback(
                        static function (
                            array $middlewares,
                            CallbackRequestHandler $requestHandler,
                            ServerRequestInterface $request
                        ) use ($routeIndependMiddleware) {
                            self::assertSame([$routeIndependMiddleware], $middlewares);

                            return $requestHandler->handle($request);
                        }
                    ),
                Call::create('dispatch')->with([$middleware], $handler, $request)->willThrowException($exception),
            ]);

            /** @var ExceptionHandlerInterface|MockObject $exceptionHandler */
            $exceptionHandler = $this->getMockByCalls(ExceptionHandlerInterface::class, [
                Call::create('createExceptionResponse')->with($request, $exception)->willReturn($response),
            ]);

            $application = new Application(
                $router,
                $middlewareDispatcher,
                $exceptionHandler,
                [$routeIndependMiddleware]
            );

            self::assertSame($response, $application->handle($request));
        }

        public function testSend(): void
        {
            /** @var StreamInterface|MockObject $responseBody */
            $responseBody = $this->getMockByCalls(StreamInterface::class, [
                Call::create('isSeekable')->with()->willReturn(true),
                Call::create('rewind')->with(),
                Call::create('eof')->with()->willReturn(false),
                Call::create('read')->with(256)->willReturn('sample body'),
                Call::create('eof')->with()->willReturn(true),
            ]);

            /** @var ResponseInterface|MockObject $response */
            $response = $this->getMockByCalls(ResponseInterface::class, [
                Call::create('getStatusCode')->with()->willReturn(200),
                Call::create('getProtocolVersion')->with()->willReturn('1.1'),
                Call::create('getReasonPhrase')->with()->willReturn('OK'),
                Call::create('getHeaders')->with()->willReturn(['X-Name' => ['value1', 'value2']]),
                Call::create('getBody')->with()->willReturn($responseBody),
            ]);

            /** @var RouterInterface|MockObject $router */
            $router = $this->getMockByCalls(RouterInterface::class);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class);

            /** @var ExceptionHandlerInterface|MockObject $exceptionHandler */
            $exceptionHandler = $this->getMockByCalls(ExceptionHandlerInterface::class);

            $application = new Application(
                $router,
                $middlewareDispatcher,
                $exceptionHandler
            );

            TestHeader::reset();

            ob_start();

            $application->send($response);

            self::assertEquals([
                [
                    'header' => 'HTTP/1.1 200 OK',
                    'replace' => true,
                    'http_response_code' => 200,
                ],
                [
                    'header' => 'X-Name: value1',
                    'replace' => false,
                    'http_response_code' => null,
                ],
                [
                    'header' => 'X-Name: value2',
                    'replace' => false,
                    'http_response_code' => null,
                ],
            ], TestHeader::all());

            self::assertSame('sample body', ob_get_clean());
        }
    }
}
