<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\UrlGeneratorMiddleware;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\UrlGeneratorMiddleware
 *
 * @internal
 */
final class UrlGeneratorMiddlewareTest extends TestCase
{
    public function testProcess(): void
    {
        $builder = new MockObjectBuilder();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $builder->create(UrlGeneratorInterface::class, []);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturnSelf('withAttribute', ['urlGenerator', $urlGenerator]),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        $middleware = new UrlGeneratorMiddleware($urlGenerator);

        self::assertSame($response, $middleware->process($request, $handler));
    }
}
