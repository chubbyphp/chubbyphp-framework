<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\MiddlewareRequestHandler;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\MiddlewareRequestHandler
 *
 * @internal
 */
final class MiddlewareRequestHandlerTest extends TestCase
{
    public function testHandle(): void
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

        /** @var MiddlewareInterface $middleware */
        $middleware = $builder->create(MiddlewareInterface::class, [
            new WithCallback(
                'process',
                static fn (ServerRequestInterface $request, RequestHandlerInterface $requestHandler) => $requestHandler->handle($request)
            ),
        ]);

        $middlewareRequestHandler = new MiddlewareRequestHandler($middleware, $handler);

        self::assertSame($response, $middlewareRequestHandler->handle($request));
    }
}
