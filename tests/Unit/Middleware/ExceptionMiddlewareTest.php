<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockMethod\WithException;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testProcess(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $middleware = new ExceptionMiddleware($responseFactory);

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithClientHttpExceptionWithoutLogger(): void
    {
        $httpException = HttpException::createImateapot([
            'key1' => 'value1',
            'key2' => 'value2',
        ], new \LogicException('logic exception', 42));

        $builder = new MockObjectBuilder();

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        $expectedBody = <<<'EOT'
            <!DOCTYPE html>
            <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <title>I'm a teapot</title>
                    <style>
                        html {
                            font-family: Helvetica, Arial, Verdana, sans-serif;
                            line-height: 1.5;
                            tab-size: 4;
                        }

                        body {
                            margin: 0;
                        }

                        * {
                            border-width: 0;
                            border-style: solid;
                        }

                        .container {
                            width: 100%
                        }

                        .mx-auto {
                            margin-left: auto;
                            margin-right: auto;
                        }

                        .mt-12 {
                            margin-top: 3rem;
                        }

                        .mb-12 {
                            margin-bottom: 3rem;
                        }

                        .text-gray-400 {
                            --tw-text-opacity: 1;
                            color: rgba(156, 163, 175, var(--tw-text-opacity));
                        }

                        .text-5xl {
                            font-size: 3rem;
                            line-height: 1;
                        }

                        .text-right {
                            text-align: right;
                        }

                        .tracking-tighter {
                            letter-spacing: -.05em;
                        }

                        .flex {
                            display: flex;
                        }

                        .flex-row {
                            flex-direction: row;
                        }

                        .basis-2\/12 {
                            flex-basis: 16.666667%;
                        }

                        .basis-10\/12 {
                            flex-basis: 83.333333%;
                        }

                        .space-x-8>:not([hidden])~:not([hidden]) {
                            --tw-space-x-reverse: 0;
                            margin-right: calc(2rem * var(--tw-space-x-reverse));
                            margin-left: calc(2rem * calc(1 - var(--tw-space-x-reverse)))
                        }

                        .gap-x-4 {
                            column-gap: 1rem;
                        }

                        .gap-y-1\.5 {
                            row-gap: 0.375rem;
                        }

                        .grid-cols-1 {
                            grid-template-columns: repeat(1, minmax(0, 1fr));
                        }

                        .grid {
                            display: grid;
                        }

                        @media (min-width:640px) {
                            .container {
                                max-width: 640px
                            }
                        }

                        @media (min-width:768px) {
                            .container {
                                max-width: 768px
                            }

                            .md\:grid-cols-8 {
                                grid-template-columns: repeat(8, minmax(0, 1fr));
                            }

                            .md\:col-span-7 {
                                grid-column: span 7/span 7
                            }
                        }

                        @media (min-width:1024px) {
                            .container {
                                max-width: 1024px
                            }
                        }

                        @media (min-width:1280px) {
                            .container {
                                max-width: 1280px
                            }
                        }

                        @media (min-width:1536px) {
                            .container {
                                max-width: 1536px
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="container mx-auto tracking-tighter mt-12">
                        <div class="flex flex-row space-x-8">
                            <div class="basis-1/12 text-5xl text-gray-400 text-right">418</div>
                            <div class="basis-11/12">
                                <span class="text-5xl">I'm a teapot</span>
                            </div>
                        </div>
                    </div>
                </body>
            </html>
            EOT;

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $builder->create(StreamInterface::class, [
            new WithReturn('write', [$expectedBody], \strlen($expectedBody)),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Content-Type', 'text/html']),
            new WithReturn('getBody', [], $responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithException('handle', [$request], $httpException),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [418, ''], $response),
        ]);

        $middleware = new ExceptionMiddleware($responseFactory);

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithClientHttpExceptionWithLogger(): void
    {
        $httpException = HttpException::createNotFound([
            'detail' => 'Could not found route "/unknown"',
            'instance' => 'instance-1234',
        ], new \LogicException('logic exception', 42));

        $builder = new MockObjectBuilder();

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        $expectedBody = <<<'EOT'
            <!DOCTYPE html>
            <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <title>Not Found</title>
                    <style>
                        html {
                            font-family: Helvetica, Arial, Verdana, sans-serif;
                            line-height: 1.5;
                            tab-size: 4;
                        }

                        body {
                            margin: 0;
                        }

                        * {
                            border-width: 0;
                            border-style: solid;
                        }

                        .container {
                            width: 100%
                        }

                        .mx-auto {
                            margin-left: auto;
                            margin-right: auto;
                        }

                        .mt-12 {
                            margin-top: 3rem;
                        }

                        .mb-12 {
                            margin-bottom: 3rem;
                        }

                        .text-gray-400 {
                            --tw-text-opacity: 1;
                            color: rgba(156, 163, 175, var(--tw-text-opacity));
                        }

                        .text-5xl {
                            font-size: 3rem;
                            line-height: 1;
                        }

                        .text-right {
                            text-align: right;
                        }

                        .tracking-tighter {
                            letter-spacing: -.05em;
                        }

                        .flex {
                            display: flex;
                        }

                        .flex-row {
                            flex-direction: row;
                        }

                        .basis-2\/12 {
                            flex-basis: 16.666667%;
                        }

                        .basis-10\/12 {
                            flex-basis: 83.333333%;
                        }

                        .space-x-8>:not([hidden])~:not([hidden]) {
                            --tw-space-x-reverse: 0;
                            margin-right: calc(2rem * var(--tw-space-x-reverse));
                            margin-left: calc(2rem * calc(1 - var(--tw-space-x-reverse)))
                        }

                        .gap-x-4 {
                            column-gap: 1rem;
                        }

                        .gap-y-1\.5 {
                            row-gap: 0.375rem;
                        }

                        .grid-cols-1 {
                            grid-template-columns: repeat(1, minmax(0, 1fr));
                        }

                        .grid {
                            display: grid;
                        }

                        @media (min-width:640px) {
                            .container {
                                max-width: 640px
                            }
                        }

                        @media (min-width:768px) {
                            .container {
                                max-width: 768px
                            }

                            .md\:grid-cols-8 {
                                grid-template-columns: repeat(8, minmax(0, 1fr));
                            }

                            .md\:col-span-7 {
                                grid-column: span 7/span 7
                            }
                        }

                        @media (min-width:1024px) {
                            .container {
                                max-width: 1024px
                            }
                        }

                        @media (min-width:1280px) {
                            .container {
                                max-width: 1280px
                            }
                        }

                        @media (min-width:1536px) {
                            .container {
                                max-width: 1536px
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="container mx-auto tracking-tighter mt-12">
                        <div class="flex flex-row space-x-8">
                            <div class="basis-1/12 text-5xl text-gray-400 text-right">404</div>
                            <div class="basis-11/12">
                                <span class="text-5xl">Not Found</span><p>Could not found route "/unknown"</p><p>instance-1234</p>
                            </div>
                        </div>
                    </div>
                </body>
            </html>
            EOT;

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $builder->create(StreamInterface::class, [
            new WithReturn('write', [$expectedBody], \strlen($expectedBody)),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Content-Type', 'text/html']),
            new WithReturn('getBody', [], $responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithException('handle', [$request], $httpException),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [404, ''], $response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithCallback('info', static function (string $message, array $context): void {
                self::assertSame('Http Exception', $message);
                self::assertArrayHasKey('data', $context);
                $data = $context['data'];
                self::assertSame([
                    'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.5',
                    'status' => 404,
                    'title' => 'Not Found',
                    'detail' => 'Could not found route "/unknown"',
                    'instance' => 'instance-1234',
                ], $data);
                self::assertArrayHasKey('exceptions', $context);
                $exceptions = $context['exceptions'];
                self::assertCount(2, $exceptions);
                $exception1 = $exceptions[0];
                self::assertArrayHasKey('message', $exception1);
                self::assertArrayHasKey('code', $exception1);
                self::assertArrayHasKey('file', $exception1);
                self::assertArrayHasKey('line', $exception1);
                self::assertArrayHasKey('trace', $exception1);
                self::assertSame(HttpException::class, $exception1['class']);
                self::assertSame('Not Found', $exception1['message']);
                self::assertSame(404, $exception1['code']);
                $exception2 = $exceptions[1];
                self::assertArrayHasKey('message', $exception2);
                self::assertArrayHasKey('code', $exception2);
                self::assertArrayHasKey('file', $exception2);
                self::assertArrayHasKey('line', $exception2);
                self::assertArrayHasKey('trace', $exception2);
                self::assertSame('LogicException', $exception2['class']);
                self::assertSame('logic exception', $exception2['message']);
                self::assertSame(42, $exception2['code']);
            }),
        ]);

        $middleware = new ExceptionMiddleware($responseFactory, false, $logger);

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithServerHttpExceptionWithDebugWithLogger(): void
    {
        $httpException = HttpException::createInternalServerError([], new \LogicException('logic exception', 42));

        $builder = new MockObjectBuilder();

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        $expectedBody = <<<'EOT'
            <!DOCTYPE html>
            <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <title>Internal Server Error</title>
                    <style>
                        html {
                            font-family: Helvetica, Arial, Verdana, sans-serif;
                            line-height: 1.5;
                            tab-size: 4;
                        }

                        body {
                            margin: 0;
                        }

                        * {
                            border-width: 0;
                            border-style: solid;
                        }

                        .container {
                            width: 100%
                        }

                        .mx-auto {
                            margin-left: auto;
                            margin-right: auto;
                        }

                        .mt-12 {
                            margin-top: 3rem;
                        }

                        .mb-12 {
                            margin-bottom: 3rem;
                        }

                        .text-gray-400 {
                            --tw-text-opacity: 1;
                            color: rgba(156, 163, 175, var(--tw-text-opacity));
                        }

                        .text-5xl {
                            font-size: 3rem;
                            line-height: 1;
                        }

                        .text-right {
                            text-align: right;
                        }

                        .tracking-tighter {
                            letter-spacing: -.05em;
                        }

                        .flex {
                            display: flex;
                        }

                        .flex-row {
                            flex-direction: row;
                        }

                        .basis-2\/12 {
                            flex-basis: 16.666667%;
                        }

                        .basis-10\/12 {
                            flex-basis: 83.333333%;
                        }

                        .space-x-8>:not([hidden])~:not([hidden]) {
                            --tw-space-x-reverse: 0;
                            margin-right: calc(2rem * var(--tw-space-x-reverse));
                            margin-left: calc(2rem * calc(1 - var(--tw-space-x-reverse)))
                        }

                        .gap-x-4 {
                            column-gap: 1rem;
                        }

                        .gap-y-1\.5 {
                            row-gap: 0.375rem;
                        }

                        .grid-cols-1 {
                            grid-template-columns: repeat(1, minmax(0, 1fr));
                        }

                        .grid {
                            display: grid;
                        }

                        @media (min-width:640px) {
                            .container {
                                max-width: 640px
                            }
                        }

                        @media (min-width:768px) {
                            .container {
                                max-width: 768px
                            }

                            .md\:grid-cols-8 {
                                grid-template-columns: repeat(8, minmax(0, 1fr));
                            }

                            .md\:col-span-7 {
                                grid-column: span 7/span 7
                            }
                        }

                        @media (min-width:1024px) {
                            .container {
                                max-width: 1024px
                            }
                        }

                        @media (min-width:1280px) {
                            .container {
                                max-width: 1280px
                            }
                        }

                        @media (min-width:1536px) {
                            .container {
                                max-width: 1536px
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="container mx-auto tracking-tighter mt-12">
                        <div class="flex flex-row space-x-8">
                            <div class="basis-1/12 text-5xl text-gray-400 text-right">500</div>
                            <div class="basis-11/12">
                                <span class="text-5xl">Internal Server Error</span>
                            </div>
                        </div>
                    </div>
                </body>
            </html>
            EOT;

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $builder->create(StreamInterface::class, [
            new WithReturn('write', [$expectedBody], \strlen($expectedBody)),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Content-Type', 'text/html']),
            new WithReturn('getBody', [], $responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithException('handle', [$request], $httpException),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [500, ''], $response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithCallback('error', static function (string $message, array $context): void {
                self::assertSame('Http Exception', $message);
                self::assertArrayHasKey('data', $context);
                $data = $context['data'];
                self::assertSame([
                    'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.5.1',
                    'status' => 500,
                    'title' => 'Internal Server Error',
                    'detail' => null,
                    'instance' => null,
                ], $data);
                self::assertArrayHasKey('exceptions', $context);
                $exceptions = $context['exceptions'];
                self::assertCount(2, $exceptions);
                $runtimeException = $exceptions[0];
                self::assertArrayHasKey('message', $runtimeException);
                self::assertArrayHasKey('code', $runtimeException);
                self::assertArrayHasKey('file', $runtimeException);
                self::assertArrayHasKey('line', $runtimeException);
                self::assertArrayHasKey('trace', $runtimeException);
                self::assertSame(HttpException::class, $runtimeException['class']);
                self::assertSame('Internal Server Error', $runtimeException['message']);
                self::assertSame(500, $runtimeException['code']);
                $logicException = $exceptions[1];
                self::assertArrayHasKey('message', $logicException);
                self::assertArrayHasKey('code', $logicException);
                self::assertArrayHasKey('file', $logicException);
                self::assertArrayHasKey('line', $logicException);
                self::assertArrayHasKey('trace', $logicException);
                self::assertSame('LogicException', $logicException['class']);
                self::assertSame('logic exception', $logicException['message']);
                self::assertSame(42, $logicException['code']);
            }),
        ]);

        $middleware = new ExceptionMiddleware($responseFactory, false, $logger);

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithExceptionWithDebugWithLogger(): void
    {
        $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

        $builder = new MockObjectBuilder();

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var MockObject|StreamInterface $responseBody */
        $responseBody = $builder->create(StreamInterface::class, [
            new WithCallback('write', static function (string $html): int {
                self::assertStringContainsString(
                    '<p>A website error has occurred. Sorry for the temporary inconvenience.</p>',
                    $html
                );
                self::assertStringContainsString('<div><strong>Class</strong></div>', $html);
                self::assertStringContainsString('<div class="md:col-span-7">RuntimeException</div>', $html);
                self::assertStringContainsString('<div><strong>Message</strong></div>', $html);
                self::assertStringContainsString('<div class="md:col-span-7">runtime exception</div>', $html);
                self::assertStringContainsString('<div><strong>Code</strong></div>', $html);
                self::assertStringContainsString('<div class="md:col-span-7">418</div>', $html);

                self::assertStringContainsString('<div><strong>Class</strong></div>', $html);
                self::assertStringContainsString('<div class="md:col-span-7">LogicException</div>', $html);
                self::assertStringContainsString('<div><strong>Message</strong></div>', $html);
                self::assertStringContainsString('<div class="md:col-span-7">logic exception</div>', $html);
                self::assertStringContainsString('<div><strong>Code</strong></div>', $html);
                self::assertStringContainsString('<div class="md:col-span-7">42</div>', $html);

                return \strlen($html);
            }),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Content-Type', 'text/html']),
            new WithReturn('getBody', [], $responseBody),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithException('handle', [$request], $exception),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [500, ''], $response),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithCallback('error', static function (string $message, array $context): void {
                self::assertSame('Http Exception', $message);
                self::assertArrayHasKey('data', $context);
                $data = $context['data'];
                self::assertSame([
                    'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.5.1',
                    'status' => 500,
                    'title' => 'Internal Server Error',
                    'detail' => 'A website error has occurred. Sorry for the temporary inconvenience.',
                    'instance' => null,
                ], $data);
                self::assertArrayHasKey('exceptions', $context);
                $exceptions = $context['exceptions'];
                self::assertCount(3, $exceptions);
                $exception1 = $exceptions[0];
                self::assertArrayHasKey('message', $exception1);
                self::assertArrayHasKey('code', $exception1);
                self::assertArrayHasKey('file', $exception1);
                self::assertArrayHasKey('line', $exception1);
                self::assertArrayHasKey('trace', $exception1);
                self::assertSame(HttpException::class, $exception1['class']);
                self::assertSame('Internal Server Error', $exception1['message']);
                self::assertSame(500, $exception1['code']);
                $exception2 = $exceptions[1];
                self::assertArrayHasKey('message', $exception2);
                self::assertArrayHasKey('code', $exception2);
                self::assertArrayHasKey('file', $exception2);
                self::assertArrayHasKey('line', $exception2);
                self::assertArrayHasKey('trace', $exception2);
                self::assertSame('RuntimeException', $exception2['class']);
                self::assertSame('runtime exception', $exception2['message']);
                self::assertSame(418, $exception2['code']);
                $exception3 = $exceptions[2];
                self::assertArrayHasKey('message', $exception3);
                self::assertArrayHasKey('code', $exception3);
                self::assertArrayHasKey('file', $exception3);
                self::assertArrayHasKey('line', $exception3);
                self::assertArrayHasKey('trace', $exception3);
                self::assertSame('LogicException', $exception3['class']);
                self::assertSame('logic exception', $exception3['message']);
                self::assertSame(42, $exception3['code']);
            }),
        ]);

        $middleware = new ExceptionMiddleware($responseFactory, true, $logger);

        self::assertSame($response, $middleware->process($request, $handler));
    }
}
