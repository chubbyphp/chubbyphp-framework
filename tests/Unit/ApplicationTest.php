<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Emitter\EmitterInterface;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
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

        /** @var MiddlewareInterface $routeIndependentMiddleware */
        $routeIndependentMiddleware = $builder->create(MiddlewareInterface::class, []);

        /** @var MiddlewareInterface $middleware */
        $middleware = $builder->create(MiddlewareInterface::class, []);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        /** @var MockObject|RouteInterface $route */
        $route = $builder->create(RouteInterface::class, [
            new WithReturn('getMiddlewares', [], [$middleware]),
            new WithReturn('getRequestHandler', [], $handler),
        ]);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['route', null], $route),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var MiddlewareDispatcherInterface $middlewareDispatcher */
        $middlewareDispatcher = $builder->create(MiddlewareDispatcherInterface::class, [
            new WithCallback(
                'dispatch',
                static function (array $middlewares, RequestHandlerInterface $requestHandler, ServerRequestInterface $req) use ($routeIndependentMiddleware): ResponseInterface {
                    TestCase::assertSame([$routeIndependentMiddleware], $middlewares);

                    return $requestHandler->handle($req);
                }
            ),
            new WithReturn('dispatch', [[$middleware], $handler, $request], $response),
        ]);

        $application = new Application([
            $routeIndependentMiddleware,
        ], $middlewareDispatcher);

        self::assertSame($response, $application($request));
    }

    public function testHandle(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MiddlewareInterface $routeIndependentMiddleware */
        $routeIndependentMiddleware = $builder->create(MiddlewareInterface::class, []);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var MockObject|ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var MiddlewareDispatcherInterface $middlewareDispatcher */
        $middlewareDispatcher = $builder->create(MiddlewareDispatcherInterface::class, [
            new WithCallback(
                'dispatch',
                static function (array $middlewares, RequestHandlerInterface $requestHandler, ServerRequestInterface $req) use ($routeIndependentMiddleware): ResponseInterface {
                    TestCase::assertSame([$routeIndependentMiddleware], $middlewares);

                    return $requestHandler->handle($req);
                }
            ),
        ]);

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        $application = new Application([
            $routeIndependentMiddleware,
        ], $middlewareDispatcher, $requestHandler);

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

        $application = new Application([], null, null, $emitter);
        $application->emit($response);
    }
}
