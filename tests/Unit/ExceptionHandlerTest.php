<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit;

use Chubbyphp\Framework\ExceptionHandler;
use Chubbyphp\Framework\Router\RouterException;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \Chubbyphp\Framework\ExceptionHandler
 */
final class ExceptionHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateRouterExceptionResponse(): void
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
            Call::create('withHeader')->with('Content-Type', 'text/html')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $routeException = RouterException::createForNotFound('/');

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(404, '')->willReturn($response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('info')->with('Route exception', [
                'title' => 'Page not found',
                'message' => 'The page "/" you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly.',
                'code' => 404,
            ]),
        ]);

        $responseHandler = new ExceptionHandler($responseFactory, false, $logger);

        self::assertSame($response, $responseHandler->createRouterExceptionResponse($request, $routeException));
    }

    public function testCreateRouterExceptionResponseInDebugMode(): void
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
            Call::create('withHeader')->with('Content-Type', 'text/html')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $routeException = RouterException::createForNotFound('/');

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(404, '')->willReturn($response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('info')->with('Route exception', [
                'title' => 'Page not found',
                'message' => 'The page "/" you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly.',
                'code' => 404,
            ]),
        ]);

        $responseHandler = new ExceptionHandler($responseFactory, false, $logger);

        self::assertSame($response, $responseHandler->createRouterExceptionResponse($request, $routeException));
    }

    public function testCreateExceptionResponse(): void
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
            Call::create('withHeader')->with('Content-Type', 'text/html')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('error')->with(
                'Exception',
                new ArgumentCallback(function (array $context) {
                    self::assertArrayHasKey('exceptions', $context);

                    $exceptions = $context['exceptions'];

                    self::assertCount(2, $exceptions);

                    $runtimeException = $exceptions[0];

                    self::assertArrayHasKey('message', $runtimeException);
                    self::assertArrayHasKey('code', $runtimeException);
                    self::assertArrayHasKey('file', $runtimeException);
                    self::assertArrayHasKey('line', $runtimeException);
                    self::assertArrayHasKey('trace', $runtimeException);

                    self::assertSame('RuntimeException', $runtimeException['class']);
                    self::assertSame('runtime exception', $runtimeException['message']);
                    self::assertSame(418, $runtimeException['code']);

                    $logicException = $exceptions[1];

                    self::assertArrayHasKey('message', $logicException);
                    self::assertArrayHasKey('code', $logicException);
                    self::assertArrayHasKey('file', $logicException);
                    self::assertArrayHasKey('line', $logicException);
                    self::assertArrayHasKey('trace', $logicException);

                    self::assertSame('LogicException', $logicException['class']);
                    self::assertSame('logic exception', $logicException['message']);
                    self::assertSame(42, $logicException['code']);
                })
            ),
        ]);

        $responseHandler = new ExceptionHandler($responseFactory, false, $logger);

        self::assertSame($response, $responseHandler->createExceptionResponse($request, $exception));
    }

    public function testCreateExceptionResponseWithoutLogger(): void
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
            Call::create('withHeader')->with('Content-Type', 'text/html')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        $responseHandler = new ExceptionHandler($responseFactory);

        self::assertSame($response, $responseHandler->createExceptionResponse($request, $exception));
    }

    public function testCreateExceptionResponseInDebugMode(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var StreamInterface|MockObject $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')
                ->with(new ArgumentCallback(function (string $html) {
                    self::assertStringContainsString(
                        'A website error has occurred. Sorry for the temporary inconvenience.',
                        $html
                    );
                    self::assertStringContainsString('div class="block"', $html);
                    self::assertStringContainsString('div class="key"', $html);
                    self::assertStringContainsString('div class="value"', $html);
                    self::assertStringContainsString('RuntimeException', $html);
                    self::assertStringContainsString('runtime exception', $html);
                    self::assertStringContainsString('418', $html);
                    self::assertStringContainsString('LogicException', $html);
                    self::assertStringContainsString('logic exception', $html);
                    self::assertStringContainsString('42', $html);
                })),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'text/html')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('error')->with(
                'Exception',
                new ArgumentCallback(function (array $context) {
                    self::assertArrayHasKey('exceptions', $context);

                    $exceptions = $context['exceptions'];

                    self::assertCount(2, $exceptions);

                    $runtimeException = $exceptions[0];

                    self::assertArrayHasKey('message', $runtimeException);
                    self::assertArrayHasKey('code', $runtimeException);
                    self::assertArrayHasKey('file', $runtimeException);
                    self::assertArrayHasKey('line', $runtimeException);
                    self::assertArrayHasKey('trace', $runtimeException);

                    self::assertSame('RuntimeException', $runtimeException['class']);
                    self::assertSame('runtime exception', $runtimeException['message']);
                    self::assertSame(418, $runtimeException['code']);

                    $logicException = $exceptions[1];

                    self::assertArrayHasKey('message', $logicException);
                    self::assertArrayHasKey('code', $logicException);
                    self::assertArrayHasKey('file', $logicException);
                    self::assertArrayHasKey('line', $logicException);
                    self::assertArrayHasKey('trace', $logicException);

                    self::assertSame('LogicException', $logicException['class']);
                    self::assertSame('logic exception', $logicException['message']);
                    self::assertSame(42, $logicException['code']);
                })
            ),
        ]);

        $responseHandler = new ExceptionHandler($responseFactory, true, $logger);

        self::assertSame($response, $responseHandler->createExceptionResponse($request, $exception));
    }
}
