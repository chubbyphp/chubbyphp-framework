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
    use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
    use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
    use Chubbyphp\Framework\Router\RouteInterface;
    use Chubbyphp\Framework\Router\RouterException;
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

            $application = new Application([]);

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
