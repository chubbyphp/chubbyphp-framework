<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\RouteMatcherMiddleware
 *
 * @internal
 */
final class RouteMatcherMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testProcess(): void
    {
        /** @var MockObject|RouteInterface $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getAttributes')->with()->willReturn(['key' => 'value']),
        ]);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('withAttribute')->with('route', $route)->willReturnSelf(),
            Call::create('withAttribute')->with('key', 'value')->willReturnSelf(),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var MockObject|RouteMatcherInterface $router */
        $router = $this->getMockByCalls(RouteMatcherInterface::class, [
            Call::create('match')->with($request)->willReturn($route),
        ]);

        $middleware = new RouteMatcherMiddleware($router);

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithException(): void
    {
        $httpException = HttpException::createNotFound([
            'detail' => 'The page "/" you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly.',
        ]);

        $this->expectExceptionObject($httpException);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MockObject|RouteMatcherInterface $router */
        $router = $this->getMockByCalls(RouteMatcherInterface::class, [
            Call::create('match')->with($request)->willThrowException($httpException),
        ]);

        $middleware = new RouteMatcherMiddleware($router);

        $middleware->process($request, $handler);
    }
}
