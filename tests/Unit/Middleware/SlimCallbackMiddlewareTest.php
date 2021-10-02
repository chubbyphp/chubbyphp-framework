<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\SlimCallbackMiddleware;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\SlimCallbackMiddleware
 *
 * @internal
 */
final class SlimCallbackMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testProcessWithoutExistingResponse(): void
    {
        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('response', null)->willReturn(null),
            Call::create('withAttribute')->with('response', $response)->willReturnSelf(),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(200, '')->willReturn($response),
        ]);

        $middleware = new SlimCallbackMiddleware(
            static function (
                ServerRequestInterface $req,
                ResponseInterface $res,
                callable $next
            ) use ($request, $response) {
                self::assertSame($request, $req);
                self::assertSame($response, $res);

                return $next($req, $res);
            },
            $responseFactory
        );

        self::assertSame($response, $middleware->process($request, $handler));
    }

    public function testProcessWithExistingResponse(): void
    {
        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('response', null)->willReturn($response),
            Call::create('withAttribute')->with('response', $response)->willReturnSelf(),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        $middleware = new SlimCallbackMiddleware(
            static function (
                ServerRequestInterface $req,
                ResponseInterface $res,
                callable $next
            ) use ($request, $response) {
                self::assertSame($request, $req);
                self::assertSame($response, $res);

                return $next($req, $res);
            },
            $responseFactory
        );

        self::assertSame($response, $middleware->process($request, $handler));
    }
}
