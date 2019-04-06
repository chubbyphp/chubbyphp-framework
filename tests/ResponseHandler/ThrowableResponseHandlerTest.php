<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\ResponseHandler;

use Chubbyphp\Framework\ResponseHandler\ThrowableResponseHandler;
use Chubbyphp\Framework\Router\RouteException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Chubbyphp\Mock\Argument\ArgumentCallback;

/**
 * @covers \Chubbyphp\Framework\ResponseHandler\ThrowableResponseHandler
 */
final class ThrowableResponseHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateRouteExceptionResponse(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $html = <<<'EOT'
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

            strong {
                display: inline-block;
                width: 65px;
            }
        </style>
    </head>
    <body>
        <h1>Page not found</h1>
        <p>The page "/" you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly.</p>
    </body>
</html>
EOT;

        /** @var StreamInterface|MockObject $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($html),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $routeException = RouteException::createForNotFound('/');

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(404, '')->willReturn($response),
        ]);

        $responseHandler = new ThrowableResponseHandler($responseFactory);

        self::assertSame($response, $responseHandler->createRouteExceptionResponse($request, $routeException));
    }

    public function testCreateRouteExceptionResponseInDebugMode(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $html = <<<'EOT'
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

            strong {
                display: inline-block;
                width: 65px;
            }
        </style>
    </head>
    <body>
        <h1>Page not found</h1>
        <p>The page "/" you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly.</p>
    </body>
</html>
EOT;

        /** @var StreamInterface|MockObject $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($html),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $routeException = RouteException::createForNotFound('/');

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(404, '')->willReturn($response),
        ]);

        $responseHandler = new ThrowableResponseHandler($responseFactory, true);

        self::assertSame($response, $responseHandler->createRouteExceptionResponse($request, $routeException));
    }

    public function testCreateThrowableResponse(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $html = <<<'EOT'
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Application Error</title>
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

            strong {
                display: inline-block;
                width: 65px;
            }
        </style>
    </head>
    <body>
        <h1>Application Error</h1>
        <p>A website error has occurred. Sorry for the temporary inconvenience.</p>
    </body>
</html>
EOT;

        /** @var StreamInterface|MockObject $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($html),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $exception = new \RuntimeException('runtime exceptiion', 418, new \Exception('exception', 42));

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        $responseHandler = new ThrowableResponseHandler($responseFactory);

        self::assertSame($response, $responseHandler->createThrowableResponse($request, $exception));
    }

    public function testCreateThrowableResponseInDebugMode(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var StreamInterface|MockObject $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')
                ->with(new ArgumentCallback(function (string $html) {
                    self::assertStringContainsString('RuntimeException', $html);
                    self::assertStringContainsString('runtime exception', $html);
                    self::assertStringContainsString('418', $html);
                    self::assertStringContainsString('Previous exception', $html);
                    self::assertStringContainsString('LogicException', $html);
                    self::assertStringContainsString('logic exception', $html);
                    self::assertStringContainsString('42', $html);
                })),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        $responseHandler = new ThrowableResponseHandler($responseFactory, true);

        self::assertSame($response, $responseHandler->createThrowableResponse($request, $exception));
    }
}
