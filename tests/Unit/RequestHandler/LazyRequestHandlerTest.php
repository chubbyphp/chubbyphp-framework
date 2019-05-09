<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\RequestHandler;

use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\RequestHandler\LazyRequestHandler
 */
final class LazyRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testHandle(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var RequestHandlerInterface|MockObject $requestHander */
        $requestHander = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($requestHander),
        ]);

        $lazyRequestHandler = new LazyRequestHandler($container, 'serviceName');

        self::assertSame($response, $lazyRequestHandler->handle($request));
    }

    public function testHandleWithWrongObject(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\RequestHandler\LazyRequestHandler::handle() expects service with id "serviceName"'
                .' to be Psr\Http\Server\RequestHandlerInterface, stdClass given'
        );

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        $requestHander = new \stdClass();

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($requestHander),
        ]);

        $lazyRequestHandler = new LazyRequestHandler($container, 'serviceName');

        self::assertSame($response, $lazyRequestHandler->handle($request));
    }

    public function testHandleWithString(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Chubbyphp\Framework\RequestHandler\LazyRequestHandler::handle() expects service with id "serviceName"'
                .' to be Psr\Http\Server\RequestHandlerInterface, string given'
        );

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        $requestHander = '';

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($requestHander),
        ]);

        $lazyRequestHandler = new LazyRequestHandler($container, 'serviceName');

        self::assertSame($response, $lazyRequestHandler->handle($request));
    }
}
