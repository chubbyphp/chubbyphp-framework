<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Middleware;

use Chubbyphp\Framework\Middleware\CallbackMiddleware;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Middleware\CallbackMiddleware
 *
 * @internal
 */
final class CallbackMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testHandle(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        $callbackMiddleware = new CallbackMiddleware(
            function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($response) {
                return $handler->handle($request);
            }
        );

        self::assertSame($response, $callbackMiddleware->process($request, $handler));
    }
}
