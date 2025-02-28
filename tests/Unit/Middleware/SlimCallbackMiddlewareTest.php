<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\SlimCallbackMiddleware;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\SlimCallbackMiddleware
 *
 * @internal
 */
final class SlimCallbackMiddlewareTest extends TestCase
{
    public function testProcessWithoutExistingResponse(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['response', null], null),
            new WithReturnSelf('withAttribute', ['response', $response]),
        ]);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [200, ''], $response),
        ]);

        $middleware = new SlimCallbackMiddleware(
            static function (
                ServerRequestInterface $req,
                ResponseInterface $res,
                callable $next
            ) use ($request, $response) {
                self::assertSame($request, $req);
                self::assertSame($response, $res);

                return $next($req, $res);
            },
            $responseFactory
        );

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithExistingResponse(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['response', null], $response),
            new WithReturnSelf('withAttribute', ['response', $response]),
        ]);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $middleware = new SlimCallbackMiddleware(
            static function (
                ServerRequestInterface $req,
                ResponseInterface $res,
                callable $next
            ) use ($request, $response) {
                self::assertSame($request, $req);
                self::assertSame($response, $res);

                return $next($req, $res);
            },
            $responseFactory
        );

        self::assertSame($response, $middleware->process($request, $handler));
    }
}
