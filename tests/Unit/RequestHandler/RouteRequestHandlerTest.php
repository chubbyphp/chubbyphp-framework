<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\RequestHandler;

use Chubbyphp\Framework\RequestHandler\RouteRequestHandler;
use Chubbyphp\Framework\Router\Exceptions\MissingRouteAttributeOnRequestException;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\RequestHandler\RouteRequestHandler
 *
 * @internal
 */
final class RouteRequestHandlerTest extends TestCase
{
    public function testHandleWithoutRoute(): void
    {
        $this->expectException(MissingRouteAttributeOnRequestException::class);
        $this->expectExceptionMessage(
            'Request attribute "route" missing or wrong type "null", please add the'
            .' "Chubbyphp\Framework\Middleware\RouteMatcherMiddleware" middleware'
        );

        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['route', null], null),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        $requestHandler = new RouteRequestHandler();

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testHandleWithRoute(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseInterface $response */
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

        /** @var RequestHandlerInterface $innerRequestHandler */
        $innerRequestHandler = $builder->create(RequestHandlerInterface::class, [
            new WithCallback(
                'handle',
                static fn (
                    ServerRequestInterface $request,
                ) => $response,
            ),
        ]);

        /** @var RouteInterface $route */
        $route = $builder->create(RouteInterface::class, [
            new WithReturn('getMiddlewares', [], [$middleware]),
            new WithReturn('getRequestHandler', [], $innerRequestHandler),
        ]);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['route', null], $route),
        ]);

        $requestHandler = new RouteRequestHandler();

        self::assertSame($response, $requestHandler->handle($request));
    }
}
