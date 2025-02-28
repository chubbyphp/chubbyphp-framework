<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\RequestHandler;

use Chubbyphp\Framework\RequestHandler\SlimCallbackRequestHandler;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testHandleWithoutExistingResponse(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['response', null], null),
            new WithReturn('getAttributes', [], ['key1' => 'value1', 'key2' => 'value2']),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [200, ''], $response),
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
        $builder = new MockObjectBuilder();

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['response', null], $response),
            new WithReturn(
                'getAttributes',
                [],
                ['key1' => 'value1', 'key2' => 'value2', 'response' => $response]
            ),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

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
