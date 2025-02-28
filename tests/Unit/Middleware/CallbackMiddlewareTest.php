<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\CallbackMiddleware;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\CallbackMiddleware
 *
 * @internal
 */
final class CallbackMiddlewareTest extends TestCase
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

        $middleware = new CallbackMiddleware(
            static fn (ServerRequestInterface $request, RequestHandlerInterface $handler) => $handler->handle($request)
        );

        self::assertSame($response, $middleware->process($request, $handler));
    }
}
