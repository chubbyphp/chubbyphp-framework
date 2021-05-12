<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\Router\Exceptions\NotFoundException;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\RouteMatcherMiddleware
 *
 * @internal
 */
final class RouteMatcherMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testProcess(): void
    {
        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getAttributes')->with()->willReturn(['key' => 'value']),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('withAttribute')->with('route', $route)->willReturnSelf(),
            Call::create('withAttribute')->with('key', 'value')->willReturnSelf(),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var RouteMatcherInterface|MockObject $router */
        $router = $this->getMockByCalls(RouteMatcherInterface::class, [
            Call::create('match')->with($request)->willReturn($route),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        $middleware = new RouteMatcherMiddleware($router, $responseFactory);

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessMissingRouteWithoutLogger(): void
    {
        $routerException = NotFoundException::create('/');

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $expectedBody = <<<'EOT'
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Page not found</title>
        <style>
            body {
                margin: 0;
                padding: 30px;
                font: 12px/1.5 Helvetica, Arial, Verdana, sans-serif;
            }

            h1 {
                margin: 0;
                font-size: 48px;
                font-weight: normal;
                line-height: 48px;
            }

            .block {
                margin-bottom: 20px;
            }

            .key {
                width: 100px;
                display: inline-flex;
            }

            .value {
                display: inline-flex;
            }
        </style>
    </head>
    <body>
        <h1>Page not found</h1><p>The page "/" you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly.</p>
    </body>
</html>
EOT;

        /** @var StreamInterface|MockObject $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($expectedBody),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'text/html')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var RouteMatcherInterface|MockObject $router */
        $router = $this->getMockByCalls(RouteMatcherInterface::class, [
            Call::create('match')->with($request)->willThrowException($routerException),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(404, '')->willReturn($response),
        ]);

        $middleware = new RouteMatcherMiddleware($router, $responseFactory);

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessMissingRouteWithLogger(): void
    {
        $routerException = NotFoundException::create('/');

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $expectedBody = <<<'EOT'
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Page not found</title>
        <style>
            body {
                margin: 0;
                padding: 30px;
                font: 12px/1.5 Helvetica, Arial, Verdana, sans-serif;
            }

            h1 {
                margin: 0;
                font-size: 48px;
                font-weight: normal;
                line-height: 48px;
            }

            .block {
                margin-bottom: 20px;
            }

            .key {
                width: 100px;
                display: inline-flex;
            }

            .value {
                display: inline-flex;
            }
        </style>
    </head>
    <body>
        <h1>Page not found</h1><p>The page "/" you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly.</p>
    </body>
</html>
EOT;

        /** @var StreamInterface|MockObject $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($expectedBody),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'text/html')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var RouteMatcherInterface|MockObject $router */
        $router = $this->getMockByCalls(RouteMatcherInterface::class, [
            Call::create('match')->with($request)->willThrowException($routerException),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(404, '')->willReturn($response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('info')->with('Route exception', [
                'title' => $routerException->getTitle(),
                'message' => $routerException->getMessage(),
                'code' => $routerException->getCode(),
            ]),
        ]);

        $middleware = new RouteMatcherMiddleware($router, $responseFactory, $logger);

        self::assertSame($response, $middleware->process($request, $handler));
    }
}
