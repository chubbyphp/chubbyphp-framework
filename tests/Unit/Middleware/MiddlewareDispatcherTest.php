<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\Middleware\MiddlewareRequestHandler;
use Chubbyphp\Mock\Argument\ArgumentInstanceOf;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\MiddlewareDispatcher
 */
final class MiddlewareDispatcherTest extends TestCase
{
    use MockByCallsTrait;

    public function testWithoutMiddlewares(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        $middlewareDispatcher = new MiddlewareDispatcher();

        self::assertSame($response, $middlewareDispatcher->dispatch([], $handler, $request));
    }

    public function testWithMiddlewares(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('withAttribute')->with('middleware', 1)->willReturnSelf(),
            Call::create('withAttribute')->with('middleware', 2)->willReturnSelf(),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class, [
            Call::create('process')
                ->with($request, new ArgumentInstanceOf(MiddlewareRequestHandler::class))
                ->willReturnCallback(
                    function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
                        $request->withAttribute('middleware', 1);

                        return $handler->handle($request);
                    }
                ),
        ]);

        /** @var MiddlewareInterface|MockObject $middleware2 */
        $middleware2 = $this->getMockByCalls(MiddlewareInterface::class, [
            Call::create('process')
                ->with($request, $handler)
                ->willReturnCallback(
                    function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
                        $request->withAttribute('middleware', 2);

                        return $handler->handle($request);
                    }
                ),
        ]);

        $middlewareDispatcher = new MiddlewareDispatcher();

        self::assertSame(
            $response,
            $middlewareDispatcher->dispatch([$middleware1, $middleware2], $handler, $request)
        );
    }

    public function testWithWrongType(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s::dispatch() expects parameter 1 to be %s[], %s[] given',
                MiddlewareDispatcher::class,
                MiddlewareInterface::class,
                \stdClass::class
            )
        );

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $middleware = new \stdClass();

        $middlewareDispatcher = new MiddlewareDispatcher();

        self::assertSame(
            $response,
            $middlewareDispatcher->dispatch([$middleware], $handler, $request)
        );
    }
}
