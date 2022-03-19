<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
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
    use MockByCallsTrait;

    public function testProcess(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MiddlewareInterface|MockObject $middleware */
        $middleware = $this->getMockByCalls(MiddlewareInterface::class, [
            Call::create('process')->with($request, $handler)->willReturn($response),
        ]);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($middleware),
        ]);

        $middleware = new LazyMiddleware($container, 'serviceName');

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithWrongObject(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to undefined method stdClass::process()');

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $middleware = new \stdClass();

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($middleware),
        ]);

        $middleware = new LazyMiddleware($container, 'serviceName');
        $middleware->process($request, $handler);
    }

    public function testProcessWithString(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to a member function process() on string');

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $middleware = '';

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($middleware),
        ]);

        $middleware = new LazyMiddleware($container, 'serviceName');
        $middleware->process($request, $handler);
    }
}
