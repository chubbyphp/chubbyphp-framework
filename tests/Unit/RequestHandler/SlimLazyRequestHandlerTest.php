<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\RequestHandler;

use Chubbyphp\Framework\RequestHandler\SlimLazyRequestHandler;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Framework\RequestHandler\SlimLazyRequestHandler
 *
 * @internal
 */
final class SlimLazyRequestHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['response', null], null),
            new WithReturn('getAttributes', [], ['key1' => 'value1', 'key2' => 'value2']),
        ]);

        $originalRequestHandler = static fn (ServerRequestInterface $req, ResponseInterface $res, array $args) => $res;

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['serviceName'], $originalRequestHandler),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [200, ''], $response),
        ]);

        $requestHandler = new SlimLazyRequestHandler($container, 'serviceName', $responseFactory);

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testHandleWithWrongObject(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\RequestHandler\SlimLazyRequestHandler::handle() expects service with id "serviceName"'
                .' to be callable, stdClass given'
        );

        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        $originalRequestHandler = new \stdClass();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['serviceName'], $originalRequestHandler),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $requestHandler = new SlimLazyRequestHandler($container, 'serviceName', $responseFactory);
        $requestHandler->handle($request);
    }

    public function testHandleWithString(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\RequestHandler\SlimLazyRequestHandler::handle() expects service with id "serviceName"'
                .' to be callable, string given'
        );

        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        $originalRequestHandler = '';

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['serviceName'], $originalRequestHandler),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $requestHandler = new SlimLazyRequestHandler($container, 'serviceName', $responseFactory);
        $requestHandler->handle($request);
    }
}
