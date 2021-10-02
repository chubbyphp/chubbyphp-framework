<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\RequestHandler;

use Chubbyphp\Framework\RequestHandler\SlimLazyRequestHandler;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
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
    use MockByCallsTrait;

    public function testHandle(): void
    {
        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('response', null)->willReturn(null),
            Call::create('getAttributes')->with()->willReturn(['key1' => 'value1', 'key2' => 'value2']),
        ]);

        $requestHander = static fn (ServerRequestInterface $req, ResponseInterface $res, array $args) => $res;

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($requestHander),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(200, '')->willReturn($response),
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

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $requestHander = new \stdClass();

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($requestHander),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

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

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $requestHander = '';

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($requestHander),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        $requestHandler = new SlimLazyRequestHandler($container, 'serviceName', $responseFactory);
        $requestHandler->handle($request);
    }
}
