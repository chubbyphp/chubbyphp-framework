<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\RequestHandler;

use Chubbyphp\Framework\RequestHandler\SlimCallbackRequestHandler;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Framework\RequestHandler\SlimCallbackRequestHandler
 *
 * @internal
 */
final class SlimCallbackRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testHandleWithoutExistingResponse(): void
    {
        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('response', null)->willReturn(null),
            Call::create('getAttributes')->with()->willReturn(['key1' => 'value1', 'key2' => 'value2']),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(200, '')->willReturn($response),
        ]);

        $requestHandler = new SlimCallbackRequestHandler(
            static function (
                ServerRequestInterface $req,
                ResponseInterface $res,
                array $args
            ) use ($request, $response) {
                self::assertSame($request, $req);
                self::assertSame($response, $res);
                self::assertSame(['key1' => 'value1', 'key2' => 'value2'], $args);

                return $res;
            },
            $responseFactory
        );

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testHandleWithExistingResponse(): void
    {
        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('response', null)->willReturn($response),
            Call::create('getAttributes')
                ->with()
                ->willReturn(['key1' => 'value1', 'key2' => 'value2', 'response' => $response]),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        $requestHandler = new SlimCallbackRequestHandler(
            static function (
                ServerRequestInterface $req,
                ResponseInterface $res,
                array $args
            ) use ($request, $response) {
                self::assertSame($request, $req);
                self::assertSame($response, $res);
                self::assertSame(['key1' => 'value1', 'key2' => 'value2', 'response' => $response], $args);

                return $res;
            },
            $responseFactory
        );

        self::assertSame($response, $requestHandler->handle($request));
    }
}
