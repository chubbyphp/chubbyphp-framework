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
 *
 * @internal
 */
final class LazyRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testHandle(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RequestHandlerInterface $requestHander */
        $requestHander = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($requestHander),
        ]);

        $requestHandler = new LazyRequestHandler($container, 'serviceName');

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testHandleWithWrongObject(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to undefined method stdClass::handle()');

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $requestHander = new \stdClass();

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($requestHander),
        ]);

        $requestHandler = new LazyRequestHandler($container, 'serviceName');
        $requestHandler->handle($request);
    }

    public function testHandleWithString(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to a member function handle() on string');

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $requestHander = '';

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($requestHander),
        ]);

        $requestHandler = new LazyRequestHandler($container, 'serviceName');
        $requestHandler->handle($request);
    }
}
