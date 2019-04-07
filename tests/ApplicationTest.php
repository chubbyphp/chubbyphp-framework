<?php

declare(strict_types=1);

namespace Chubbyphp\Framework
{
    class TestHeader
    {
        /**
         * @var array
         */
        private static $headers = [];

        /**
         * @param string $header
         * @param bool   $replace
         * @param int    $http_response_code
         */
        public static function add(string $header, bool $replace = true, int $http_response_code = null): void
        {
            self::$headers[] = [
                'header' => $header,
                'replace' => $replace,
                'http_response_code' => $http_response_code,
            ];
        }

        /**
         * @return array
         */
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
     * @param string $header
     * @param bool   $replace
     * @param int    $http_response_code
     */
    function header(string $header, bool $replace = true, int $http_response_code = null): void
    {
        TestHeader::add($header, $replace, $http_response_code);
    }
}

namespace Chubbyphp\Tests\Framework
{
    use Chubbyphp\Framework\Application;
    use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
    use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandlerInterface;
    use Chubbyphp\Framework\Router\RouteDispatcherInterface;
    use Chubbyphp\Framework\Router\RouteDispatcherException;
    use Chubbyphp\Framework\Router\RouteInterface;
    use Chubbyphp\Framework\TestHeader;
    use Chubbyphp\Mock\Argument\ArgumentCallback;
    use Chubbyphp\Mock\Call;
    use Chubbyphp\Mock\MockByCallsTrait;
    use PHPUnit\Framework\MockObject\MockObject;
    use PHPUnit\Framework\TestCase;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\StreamInterface;
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Log\LoggerInterface;

    /**
     * @covers \Chubbyphp\Framework\Application
     */
    final class ApplicationTest extends TestCase
    {
        use MockByCallsTrait;

        public function testRunFoundWithoutSend(): void
        {
            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class, [
                Call::create('withAttribute')->with('_route', 'index')->willReturnSelf(),
                Call::create('withAttribute')->with('key', 'value')->willReturnSelf(),
            ]);

            /** @var ResponseInterface|MockObject $response */
            $response = $this->getMockByCalls(ResponseInterface::class);

            /** @var MiddlewareInterface|MockObject $middleware */
            $middleware = $this->getMockByCalls(MiddlewareInterface::class);

            /** @var RequestHandlerInterface|MockObject $requestHandler */
            $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

            /** @var RouteInterface|MockObject $route */
            $route = $this->getMockByCalls(RouteInterface::class, [
                Call::create('getMiddlewares')->with()->willReturn([$middleware]),
                Call::create('getRequestHandler')->with()->willReturn($requestHandler),
                Call::create('getName')->with()->willReturn('index'),
                Call::create('getAttributes')->with()->willReturn(['key' => 'value']),
            ]);

            /** @var RouteDispatcherInterface|MockObject $routeDispatcher */
            $routeDispatcher = $this->getMockByCalls(RouteDispatcherInterface::class, [
                Call::create('dispatch')->with($request)->willReturn($route),
            ]);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
                Call::create('dispatch')->with([$middleware], $requestHandler, $request)->willReturn($response),
            ]);

            /** @var ExceptionResponseHandlerInterface|MockObject $throwableResponseHandler */
            $throwableResponseHandler = $this->getMockByCalls(ExceptionResponseHandlerInterface::class);

            /** @var LoggerInterface|MockObject $logger */
            $logger = $this->getMockByCalls(LoggerInterface::class);

            $application = new Application(
                $routeDispatcher,
                $middlewareDispatcher,
                $throwableResponseHandler,
                $logger
            );

            self::assertSame($response, $application->run($request, false));
        }

        public function testRunFoundWithSend(): void
        {
            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class, [
                Call::create('withAttribute')->with('_route', 'index')->willReturnSelf(),
                Call::create('withAttribute')->with('key', 'value')->willReturnSelf(),
            ]);

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

            /** @var MiddlewareInterface|MockObject $middleware */
            $middleware = $this->getMockByCalls(MiddlewareInterface::class);

            /** @var RequestHandlerInterface|MockObject $requestHandler */
            $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

            /** @var RouteInterface|MockObject $route */
            $route = $this->getMockByCalls(RouteInterface::class, [
                Call::create('getMiddlewares')->with()->willReturn([$middleware]),
                Call::create('getRequestHandler')->with()->willReturn($requestHandler),
                Call::create('getName')->with()->willReturn('index'),
                Call::create('getAttributes')->with()->willReturn(['key' => 'value']),
            ]);

            /** @var RouteDispatcherInterface|MockObject $routeDispatcher */
            $routeDispatcher = $this->getMockByCalls(RouteDispatcherInterface::class, [
                Call::create('dispatch')->with($request)->willReturn($route),
            ]);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
                Call::create('dispatch')->with([$middleware], $requestHandler, $request)->willReturn($response),
            ]);

            /** @var ExceptionResponseHandlerInterface|MockObject $throwableResponseHandler */
            $throwableResponseHandler = $this->getMockByCalls(ExceptionResponseHandlerInterface::class);

            /** @var LoggerInterface|MockObject $logger */
            $logger = $this->getMockByCalls(LoggerInterface::class);

            $application = new Application(
                $routeDispatcher,
                $middlewareDispatcher,
                $throwableResponseHandler,
                $logger
            );

            TestHeader::reset();

            ob_start();

            self::assertSame($response, $application->run($request));

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

        public function testRunNotFoundWithoutSend(): void
        {
            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class);

            /** @var ResponseInterface|MockObject $response */
            $response = $this->getMockByCalls(ResponseInterface::class);

            $routeException = RouteDispatcherException::createForNotFound('/');

            /** @var RouteDispatcherInterface|MockObject $routeDispatcher */
            $routeDispatcher = $this->getMockByCalls(RouteDispatcherInterface::class, [
                Call::create('dispatch')->with($request)->willThrowException($routeException),
            ]);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class);

            /** @var ExceptionResponseHandlerInterface|MockObject $throwableResponseHandler */
            $throwableResponseHandler = $this->getMockByCalls(ExceptionResponseHandlerInterface::class, [
                Call::create('createRouteDispatcherExceptionResponse')->with($request, $routeException)->willReturn($response),
            ]);

            /** @var LoggerInterface|MockObject $logger */
            $logger = $this->getMockByCalls(LoggerInterface::class, [
                Call::create('info')->with('Page not found', [
                    'message' => 'The page "/" you are looking for could not be found.'
                        .' Check the address bar to ensure your URL is spelled correctly.',
                    'code' => 404,
                ]),
            ]);

            $application = new Application(
                $routeDispatcher,
                $middlewareDispatcher,
                $throwableResponseHandler,
                $logger
            );

            self::assertSame($response, $application->run($request, false));
        }

        public function testRunNotFoundWithSend(): void
        {
            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class);

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
                Call::create('getStatusCode')->with()->willReturn(404),
                Call::create('getProtocolVersion')->with()->willReturn('1.1'),
                Call::create('getReasonPhrase')->with()->willReturn('Not Found'),
                Call::create('getHeaders')->with()->willReturn(['X-Name' => ['value1', 'value2']]),
                Call::create('getBody')->with()->willReturn($responseBody),
            ]);

            $routeException = RouteDispatcherException::createForNotFound('/');

            /** @var RouteDispatcherInterface|MockObject $routeDispatcher */
            $routeDispatcher = $this->getMockByCalls(RouteDispatcherInterface::class, [
                Call::create('dispatch')->with($request)->willThrowException($routeException),
            ]);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class);

            /** @var ExceptionResponseHandlerInterface|MockObject $throwableResponseHandler */
            $throwableResponseHandler = $this->getMockByCalls(ExceptionResponseHandlerInterface::class, [
                Call::create('createRouteDispatcherExceptionResponse')->with($request, $routeException)->willReturn($response),
            ]);

            /** @var LoggerInterface|MockObject $logger */
            $logger = $this->getMockByCalls(LoggerInterface::class, [
                Call::create('info')->with('Page not found', [
                    'message' => 'The page "/" you are looking for could not be found.'
                        .' Check the address bar to ensure your URL is spelled correctly.',
                    'code' => 404,
                ]),
            ]);

            $application = new Application(
                $routeDispatcher,
                $middlewareDispatcher,
                $throwableResponseHandler,
                $logger
            );

            TestHeader::reset();

            ob_start();

            self::assertSame($response, $application->run($request));

            self::assertEquals([
                [
                    'header' => 'HTTP/1.1 404 Not Found',
                    'replace' => true,
                    'http_response_code' => 404,
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

        public function testRunThrowableWithoutSend(): void
        {
            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class, [
                Call::create('withAttribute')->with('_route', 'index')->willReturnSelf(),
                Call::create('withAttribute')->with('key', 'value')->willReturnSelf(),
            ]);

            /** @var ResponseInterface|MockObject $response */
            $response = $this->getMockByCalls(ResponseInterface::class);

            $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

            /** @var MiddlewareInterface|MockObject $middleware */
            $middleware = $this->getMockByCalls(MiddlewareInterface::class);

            /** @var RequestHandlerInterface|MockObject $requestHandler */
            $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

            /** @var RouteInterface|MockObject $route */
            $route = $this->getMockByCalls(RouteInterface::class, [
                Call::create('getMiddlewares')->with()->willReturn([$middleware]),
                Call::create('getRequestHandler')->with()->willReturn($requestHandler),
                Call::create('getName')->with()->willReturn('index'),
                Call::create('getAttributes')->with()->willReturn(['key' => 'value']),
            ]);

            /** @var RouteDispatcherInterface|MockObject $routeDispatcher */
            $routeDispatcher = $this->getMockByCalls(RouteDispatcherInterface::class, [
                Call::create('dispatch')->with($request)->willReturn($route),
            ]);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
                Call::create('dispatch')->with([$middleware], $requestHandler, $request)->willThrowException($exception),
            ]);

            /** @var ExceptionResponseHandlerInterface|MockObject $throwableResponseHandler */
            $throwableResponseHandler = $this->getMockByCalls(ExceptionResponseHandlerInterface::class, [
                Call::create('createExceptionResponse')->with($request, $exception)->willReturn($response),
            ]);

            /** @var LoggerInterface|MockObject $logger */
            $logger = $this->getMockByCalls(LoggerInterface::class, [
                Call::create('error')
                    ->with(
                        'Throwable',
                        new ArgumentCallback(function (array $context) {
                            self::assertArrayHasKey('throwables', $context);

                            $throwables = $context['throwables'];

                            self::assertCount(2, $throwables);

                            $runtimeException = $throwables[0];

                            self::assertArrayHasKey('message', $runtimeException);
                            self::assertArrayHasKey('code', $runtimeException);
                            self::assertArrayHasKey('file', $runtimeException);
                            self::assertArrayHasKey('line', $runtimeException);
                            self::assertArrayHasKey('trace', $runtimeException);

                            self::assertSame('runtime exception', $runtimeException['message']);
                            self::assertSame(418, $runtimeException['code']);

                            $logicException = $throwables[1];

                            self::assertArrayHasKey('message', $logicException);
                            self::assertArrayHasKey('code', $logicException);
                            self::assertArrayHasKey('file', $logicException);
                            self::assertArrayHasKey('line', $logicException);
                            self::assertArrayHasKey('trace', $logicException);

                            self::assertSame('logic exception', $logicException['message']);
                            self::assertSame(42, $logicException['code']);
                        })
                    ),
            ]);

            $application = new Application(
                $routeDispatcher,
                $middlewareDispatcher,
                $throwableResponseHandler,
                $logger
            );

            self::assertSame($response, $application->run($request, false));
        }

        public function testRunThrowableWithSend(): void
        {
            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class, [
                Call::create('withAttribute')->with('_route', 'index')->willReturnSelf(),
                Call::create('withAttribute')->with('key', 'value')->willReturnSelf(),
            ]);

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
                Call::create('getStatusCode')->with()->willReturn(500),
                Call::create('getProtocolVersion')->with()->willReturn('1.1'),
                Call::create('getReasonPhrase')->with()->willReturn('Internal Server Error'),
                Call::create('getHeaders')->with()->willReturn(['X-Name' => ['value1', 'value2']]),
                Call::create('getBody')->with()->willReturn($responseBody),
            ]);

            $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

            /** @var MiddlewareInterface|MockObject $middleware */
            $middleware = $this->getMockByCalls(MiddlewareInterface::class);

            /** @var RequestHandlerInterface|MockObject $requestHandler */
            $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

            /** @var RouteInterface|MockObject $route */
            $route = $this->getMockByCalls(RouteInterface::class, [
                Call::create('getMiddlewares')->with()->willReturn([$middleware]),
                Call::create('getRequestHandler')->with()->willReturn($requestHandler),
                Call::create('getName')->with()->willReturn('index'),
                Call::create('getAttributes')->with()->willReturn(['key' => 'value']),
            ]);

            /** @var RouteDispatcherInterface|MockObject $routeDispatcher */
            $routeDispatcher = $this->getMockByCalls(RouteDispatcherInterface::class, [
                Call::create('dispatch')->with($request)->willReturn($route),
            ]);

            /** @var MiddlewareDispatcherInterface|MockObject $middlewareDispatcher */
            $middlewareDispatcher = $this->getMockByCalls(MiddlewareDispatcherInterface::class, [
                Call::create('dispatch')->with([$middleware], $requestHandler, $request)->willThrowException($exception),
            ]);

            /** @var ExceptionResponseHandlerInterface|MockObject $throwableResponseHandler */
            $throwableResponseHandler = $this->getMockByCalls(ExceptionResponseHandlerInterface::class, [
                Call::create('createExceptionResponse')->with($request, $exception)->willReturn($response),
            ]);

            /** @var LoggerInterface|MockObject $logger */
            $logger = $this->getMockByCalls(LoggerInterface::class, [
                Call::create('error')
                    ->with(
                        'Throwable',
                        new ArgumentCallback(function (array $context) {
                            self::assertArrayHasKey('throwables', $context);

                            $throwables = $context['throwables'];

                            self::assertCount(2, $throwables);

                            $runtimeException = $throwables[0];

                            self::assertArrayHasKey('message', $runtimeException);
                            self::assertArrayHasKey('code', $runtimeException);
                            self::assertArrayHasKey('file', $runtimeException);
                            self::assertArrayHasKey('line', $runtimeException);
                            self::assertArrayHasKey('trace', $runtimeException);

                            self::assertSame('runtime exception', $runtimeException['message']);
                            self::assertSame(418, $runtimeException['code']);

                            $logicException = $throwables[1];

                            self::assertArrayHasKey('message', $logicException);
                            self::assertArrayHasKey('code', $logicException);
                            self::assertArrayHasKey('file', $logicException);
                            self::assertArrayHasKey('line', $logicException);
                            self::assertArrayHasKey('trace', $logicException);

                            self::assertSame('logic exception', $logicException['message']);
                            self::assertSame(42, $logicException['code']);
                        })
                    ),
            ]);

            $application = new Application(
                $routeDispatcher,
                $middlewareDispatcher,
                $throwableResponseHandler,
                $logger
            );

            TestHeader::reset();

            ob_start();

            self::assertSame($response, $application->run($request));

            self::assertEquals([
                [
                    'header' => 'HTTP/1.1 500 Internal Server Error',
                    'replace' => true,
                    'http_response_code' => 500,
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
