<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\SlimLazyMiddleware;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
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
    use MockByCallsTrait;

    public function testProcess(): void
    {
        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('response', null)->willReturn(null),
            Call::create('withAttribute')->with('response', $response)->willReturnSelf(),
        ]);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        $middleware = static fn (ServerRequestInterface $req, ResponseInterface $res, callable $next) => $next($req, $res);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($middleware),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(200, '')->willReturn($response),
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

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $middleware = new \stdClass();

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($middleware),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

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

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $middleware = '';

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('serviceName')->willReturn($middleware),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        $middleware = new SlimLazyMiddleware($container, 'serviceName', $responseFactory);
        $middleware->process($request, $handler);
    }
}
