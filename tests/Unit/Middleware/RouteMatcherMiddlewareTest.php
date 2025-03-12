<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Mock\MockMethod\WithException;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testProcess(): void
    {
        $builder = new MockObjectBuilder();

        /** @var RouteInterface $route */
        $route = $builder->create(RouteInterface::class, [
            new WithReturn('getAttributes', [], ['key' => 'value']),
        ]);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturnSelf('withAttribute', ['route', $route]),
            new WithReturnSelf('withAttribute', ['key', 'value']),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var RouteMatcherInterface $router */
        $router = $builder->create(RouteMatcherInterface::class, [
            new WithReturn('match', [$request], $route),
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

        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        /** @var RouteMatcherInterface $router */
        $router = $builder->create(RouteMatcherInterface::class, [
            new WithException('match', [$request], $httpException),
        ]);

        $middleware = new RouteMatcherMiddleware($router);

        $middleware->process($request, $handler);
    }
}
