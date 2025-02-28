<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\RequestHandler;

use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\RequestHandler\LazyRequestHandler
 *
 * @internal
 */
final class LazyRequestHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var RequestHandlerInterface $originalRequestHandler */
        $originalRequestHandler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['serviceName'], $originalRequestHandler),
        ]);

        $requestHandler = new LazyRequestHandler($container, 'serviceName');

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testHandleWithWrongObject(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\RequestHandler\LazyRequestHandler::handle() expects service with id "serviceName"'
                .' to be Psr\Http\Server\RequestHandlerInterface, stdClass given'
        );

        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        $originalRequestHandler = new \stdClass();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['serviceName'], $originalRequestHandler),
        ]);

        $requestHandler = new LazyRequestHandler($container, 'serviceName');
        $requestHandler->handle($request);
    }

    public function testHandleWithString(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\RequestHandler\LazyRequestHandler::handle() expects service with id "serviceName"'
                .' to be Psr\Http\Server\RequestHandlerInterface, string given'
        );

        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        $originalRequestHandler = '';

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['serviceName'], $originalRequestHandler),
        ]);

        $requestHandler = new LazyRequestHandler($container, 'serviceName');
        $requestHandler->handle($request);
    }
}
