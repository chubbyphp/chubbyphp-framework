<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\MiddlewareRequestHandler;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\MiddlewareRequestHandler
 */
final class MiddlewareRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testHandle(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var MiddlewareInterface|MockObject $middleware */
        $middleware = $this->getMockByCalls(MiddlewareInterface::class, [
            Call::create('process')
            ->with($request, $requestHandler)
            ->willReturnCallback(function (ServerRequestInterface $request, RequestHandlerInterface $requestHandler) {
                return $requestHandler->handle($request);
            }),
        ]);

        $middlewareRequestHandler = new MiddlewareRequestHandler($middleware, $requestHandler);

        self::assertSame($response, $middlewareRequestHandler->handle($request));
    }
}
