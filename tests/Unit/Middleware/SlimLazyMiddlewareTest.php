<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\SlimLazyMiddleware;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\SlimLazyMiddleware
 *
 * @internal
 */
final class SlimLazyMiddlewareTest extends TestCase
{
    public function testProcess(): void
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

        $originalMiddleware = static fn (
            ServerRequestInterface $req,
            ResponseInterface $res,
            callable $next
        ): ResponseInterface => $next($req, $res);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['serviceName'], $originalMiddleware),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [200, ''], $response),
        ]);

        $middleware = new SlimLazyMiddleware($container, 'serviceName', $responseFactory);

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithWrongObject(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\Middleware\SlimLazyMiddleware::process() expects service with id "serviceName"'
                .' to be callable, stdClass given'
        );

        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        $originalMiddleware = new \stdClass();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['serviceName'], $originalMiddleware),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $middleware = new SlimLazyMiddleware($container, 'serviceName', $responseFactory);
        $middleware->process($request, $handler);
    }

    public function testProcessWithString(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\Middleware\SlimLazyMiddleware::process() expects service with id "serviceName"'
                .' to be callable, string given'
        );

        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        $originalMiddleware = '';

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['serviceName'], $originalMiddleware),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $middleware = new SlimLazyMiddleware($container, 'serviceName', $responseFactory);
        $middleware->process($request, $handler);
    }
}
