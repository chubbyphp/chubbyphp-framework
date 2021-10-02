<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Mock\Argument\ArgumentCallback;
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
 * @covers \Chubbyphp\Framework\Middleware\ExceptionMiddleware
 *
 * @internal
 */
final class ExceptionMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testProcess(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        $middleware = new ExceptionMiddleware($responseFactory);

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithExceptionWithoutDebugWithoutLogger(): void
    {
        $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $expectedBody = <<<'EOT'
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
                    <h1>Application Error</h1><p>A website error has occurred. Sorry for the temporary inconvenience.</p>
                </body>
            </html>
            EOT;

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($expectedBody),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'text/html')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willThrowException($exception),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        $middleware = new ExceptionMiddleware($responseFactory);

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithExceptionWithoutDebugWithLogger(): void
    {
        $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $expectedBody = <<<'EOT'
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
                    <h1>Application Error</h1><p>A website error has occurred. Sorry for the temporary inconvenience.</p>
                </body>
            </html>
            EOT;

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($expectedBody),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'text/html')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willThrowException($exception),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('error')->with(
                'Exception',
                new ArgumentCallback(static function (array $context): void {
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

        $middleware = new ExceptionMiddleware($responseFactory, false, $logger);

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithExceptionWithDebugWithoutLogger(): void
    {
        $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')
                ->with(new ArgumentCallback(static function (string $html): void {
                    self::assertStringContainsString(
                        '<p>A website error has occurred. Sorry for the temporary inconvenience.</p>',
                        $html
                    );
                    self::assertStringContainsString('div class="block"', $html);
                    self::assertStringContainsString('<div class="key"><strong>Class</strong></div>', $html);
                    self::assertStringContainsString('<div class="value">RuntimeException</div>', $html);
                    self::assertStringContainsString('<div class="key"><strong>Message</strong></div>', $html);
                    self::assertStringContainsString('<div class="value">runtime exception</div>', $html);
                    self::assertStringContainsString('<div class="key"><strong>Code</strong></div>', $html);
                    self::assertStringContainsString('<div class="value">418</div>', $html);

                    self::assertStringContainsString('<div class="key"><strong>Class</strong></div>', $html);
                    self::assertStringContainsString('<div class="value">LogicException</div>', $html);
                    self::assertStringContainsString('<div class="key"><strong>Message</strong></div>', $html);
                    self::assertStringContainsString('<div class="value">logic exception</div>', $html);
                    self::assertStringContainsString('<div class="key"><strong>Code</strong></div>', $html);
                    self::assertStringContainsString('<div class="value">42</div>', $html);
                })),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'text/html')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willThrowException($exception),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        $middleware = new ExceptionMiddleware($responseFactory, true);

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithExceptionWithDebugWithLogger(): void
    {
        $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')
                ->with(new ArgumentCallback(static function (string $html): void {
                    self::assertStringContainsString(
                        '<p>A website error has occurred. Sorry for the temporary inconvenience.</p>',
                        $html
                    );
                    self::assertStringContainsString('div class="block"', $html);
                    self::assertStringContainsString('<div class="key"><strong>Class</strong></div>', $html);
                    self::assertStringContainsString('<div class="value">RuntimeException</div>', $html);
                    self::assertStringContainsString('<div class="key"><strong>Message</strong></div>', $html);
                    self::assertStringContainsString('<div class="value">runtime exception</div>', $html);
                    self::assertStringContainsString('<div class="key"><strong>Code</strong></div>', $html);
                    self::assertStringContainsString('<div class="value">418</div>', $html);

                    self::assertStringContainsString('<div class="key"><strong>Class</strong></div>', $html);
                    self::assertStringContainsString('<div class="value">LogicException</div>', $html);
                    self::assertStringContainsString('<div class="key"><strong>Message</strong></div>', $html);
                    self::assertStringContainsString('<div class="value">logic exception</div>', $html);
                    self::assertStringContainsString('<div class="key"><strong>Code</strong></div>', $html);
                    self::assertStringContainsString('<div class="value">42</div>', $html);
                })),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'text/html')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willThrowException($exception),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('error')->with(
                'Exception',
                new ArgumentCallback(static function (array $context): void {
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

        $middleware = new ExceptionMiddleware($responseFactory, true, $logger);

        self::assertSame($response, $middleware->process($request, $handler));
    }
}
