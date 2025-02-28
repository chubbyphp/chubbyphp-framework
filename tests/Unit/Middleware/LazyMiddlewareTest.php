<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\LazyMiddleware
 *
 * @internal
 */
final class LazyMiddlewareTest extends TestCase
{
    public function testProcess(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        /** @var MiddlewareInterface $originalMiddleware */
        $originalMiddleware = $builder->create(MiddlewareInterface::class, [
            new WithReturn('process', [$request, $handler], $response),
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['serviceName'], $originalMiddleware),
        ]);

        $middleware = new LazyMiddleware($container, 'serviceName');

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithWrongObject(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\Middleware\LazyMiddleware::process() expects service with id "serviceName"'
                .' to be Psr\Http\Server\MiddlewareInterface, stdClass given'
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

        $middleware = new LazyMiddleware($container, 'serviceName');
        $middleware->process($request, $handler);
    }

    public function testProcessWithString(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\Middleware\LazyMiddleware::process() expects service with id "serviceName"'
                .' to be Psr\Http\Server\MiddlewareInterface, string given'
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

        $middleware = new LazyMiddleware($container, 'serviceName');
        $middleware->process($request, $handler);
    }
}
