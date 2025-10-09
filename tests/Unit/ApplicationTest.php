<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Emitter\EmitterInterface;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Application
 *
 * @internal
 */
final class ApplicationTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var MiddlewareInterface $middleware */
        $middleware = $builder->create(MiddlewareInterface::class, [
            new WithCallback(
                'process',
                static fn (
                    ServerRequestInterface $request,
                    RequestHandlerInterface $requestHandler
                ) => $requestHandler->handle($request)
            ),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithCallback(
                'handle',
                static fn (
                    ServerRequestInterface $request,
                ) => $response,
            ),
        ]);

        /** @var MockObject|RouteInterface $route */
        $route = $builder->create(RouteInterface::class, [
            new WithReturn('getMiddlewares', [], [$middleware]),
            new WithReturn('getRequestHandler', [], $handler),
        ]);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['route', null], $route),
        ]);

        /** @var MiddlewareInterface $routeIndependentMiddleware */
        $routeIndependentMiddleware = $builder->create(MiddlewareInterface::class, [
            new WithCallback(
                'process',
                static fn (
                    ServerRequestInterface $request,
                    RequestHandlerInterface $requestHandler
                ) => $requestHandler->handle($request)
            ),
        ]);

        $application = new Application([
            $routeIndependentMiddleware,
        ]);

        self::assertSame($response, $application($request));
    }

    public function testHandle(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var MiddlewareInterface $middleware */
        $middleware = $builder->create(MiddlewareInterface::class, [
            new WithCallback(
                'process',
                static fn (
                    ServerRequestInterface $request,
                    RequestHandlerInterface $requestHandler
                ) => $requestHandler->handle($request)
            ),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithCallback(
                'handle',
                static fn (
                    ServerRequestInterface $request,
                ) => $response,
            ),
        ]);

        /** @var MockObject|RouteInterface $route */
        $route = $builder->create(RouteInterface::class, [
            new WithReturn('getMiddlewares', [], [$middleware]),
            new WithReturn('getRequestHandler', [], $handler),
        ]);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['route', null], $route),
        ]);

        /** @var MiddlewareInterface $routeIndependentMiddleware */
        $routeIndependentMiddleware = $builder->create(MiddlewareInterface::class, [
            new WithCallback(
                'process',
                static fn (
                    ServerRequestInterface $request,
                    RequestHandlerInterface $requestHandler
                ) => $requestHandler->handle($request)
            ),
        ]);

        $application = new Application([
            $routeIndependentMiddleware,
        ]);

        self::assertSame($response, $application->handle($request));
    }

    #[DoesNotPerformAssertions]
    public function testEmit(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var EmitterInterface $emitter */
        $emitter = $builder->create(EmitterInterface::class, [
            new WithReturn('emit', [$response], null),
        ]);

        $application = new Application([], $emitter);
        $application->emit($response);
    }
}
