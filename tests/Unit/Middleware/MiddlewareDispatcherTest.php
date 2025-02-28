<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\MiddlewareDispatcher
 *
 * @internal
 */
final class MiddlewareDispatcherTest extends TestCase
{
    public function testWithoutMiddlewares(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        $middlewareDispatcher = new MiddlewareDispatcher();

        self::assertSame($response, $middlewareDispatcher->dispatch([], $handler, $request));
    }

    public function testWithMiddlewares(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturnSelf('withAttribute', ['middleware', 1]),
            new WithReturnSelf('withAttribute', ['middleware', 2]),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var MiddlewareInterface $middleware1 */
        $middleware1 = $builder->create(MiddlewareInterface::class, [
            new WithCallback(
                'process',
                static function (
                    ServerRequestInterface $request,
                    RequestHandlerInterface $requestHandler
                ) {
                    $request = $request->withAttribute('middleware', 1);

                    return $requestHandler->handle($request);
                },
            ),
        ]);

        /** @var MiddlewareInterface $middleware2 */
        $middleware2 = $builder->create(MiddlewareInterface::class, [
            new WithCallback(
                'process',
                static function (
                    ServerRequestInterface $request,
                    RequestHandlerInterface $requestHandler
                ) {
                    $request = $request->withAttribute('middleware', 2);

                    return $requestHandler->handle($request);
                },
            ),
        ]);

        $middlewareDispatcher = new MiddlewareDispatcher();

        self::assertSame(
            $response,
            $middlewareDispatcher->dispatch([$middleware1, $middleware2], $handler, $request)
        );
    }
}
