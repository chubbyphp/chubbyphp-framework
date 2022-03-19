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
 *
 * @internal
 */
final class MiddlewareDispatcherTest extends TestCase
{
    use MockByCallsTrait;

    public function testWithoutMiddlewares(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        $middlewareDispatcher = new MiddlewareDispatcher();

        self::assertSame($response, $middlewareDispatcher->dispatch([], $handler, $request));
    }

    public function testWithMiddlewares(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('withAttribute')->with('middleware', 1)->willReturnSelf(),
            Call::create('withAttribute')->with('middleware', 2)->willReturnSelf(),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var MiddlewareInterface|MockObject $middleware1 */
        $middleware1 = $this->getMockByCalls(MiddlewareInterface::class, [
            Call::create('process')
                ->with($request, new ArgumentInstanceOf(MiddlewareRequestHandler::class))
                ->willReturnCallback(
                    static function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
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
                    static function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
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
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to undefined method stdClass::process()');

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class);

        $middleware = new \stdClass();

        $middlewareDispatcher = new MiddlewareDispatcher();
        $middlewareDispatcher->dispatch([$middleware], $handler, $request);
    }
}
