<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\ResponseHandler;

use Chubbyphp\Framework\ResponseHandler\JsonExceptionResponseHandler;
use Chubbyphp\Framework\Router\RouteMatcherException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Chubbyphp\Mock\Argument\ArgumentCallback;

/**
 * @covers \Chubbyphp\Framework\ResponseHandler\JsonExceptionResponseHandler
 */
final class JsonExceptionResponseHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateRouteMatcherExceptionResponse(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $json = json_encode([
            'type' => 'https://tools.ietf.org/html/rfc7231#section-6.5.4',
            'title' => 'Page not found',
            'detail' => 'The page "/" you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly.',
        ]);

        /** @var StreamInterface|MockObject $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($json),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/json')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $routeException = RouteMatcherException::createForNotFound('/');

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(404, '')->willReturn($response),
        ]);

        $responseHandler = new JsonExceptionResponseHandler($responseFactory);

        self::assertSame($response, $responseHandler->createRouteMatcherExceptionResponse($request, $routeException));
    }

    public function testCreateRouteMatcherExceptionResponseInDebugMode(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $json = json_encode([
            'type' => 'https://tools.ietf.org/html/rfc7231#section-6.5.4',
            'title' => 'Page not found',
            'detail' => 'The page "/" you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly.',
        ]);

        /** @var StreamInterface|MockObject $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($json),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/json')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $routeException = RouteMatcherException::createForNotFound('/');

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(404, '')->willReturn($response),
        ]);

        $responseHandler = new JsonExceptionResponseHandler($responseFactory, true);

        self::assertSame($response, $responseHandler->createRouteMatcherExceptionResponse($request, $routeException));
    }

    public function testCreateExceptionResponse(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $json = json_encode([
            'type' => 'https://tools.ietf.org/html/rfc7231#section-6.6.1',
            'title' => 'Internal Server Error',
        ]);

        /** @var StreamInterface|MockObject $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')->with($json),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/json')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $exception = new \RuntimeException('runtime exceptiion', 418, new \Exception('exception', 42));

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        $responseHandler = new JsonExceptionResponseHandler($responseFactory);

        self::assertSame($response, $responseHandler->createExceptionResponse($request, $exception));
    }

    public function testCreateExceptionResponseInDebugMode(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var StreamInterface|MockObject $responseBody */
        $responseBody = $this->getMockByCalls(StreamInterface::class, [
            Call::create('write')
                ->with(new ArgumentCallback(function (string $json) {
                    self::assertStringContainsString('RuntimeException', $json);
                    self::assertStringContainsString('runtime exception', $json);
                    self::assertStringContainsString('418', $json);
                    self::assertStringContainsString('LogicException', $json);
                    self::assertStringContainsString('logic exception', $json);
                    self::assertStringContainsString('42', $json);
                })),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/json')->willReturnSelf(),
            Call::create('getBody')->with()->willReturn($responseBody),
        ]);

        $exception = new \RuntimeException('runtime exception', 418, new \LogicException('logic exception', 42));

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(500, '')->willReturn($response),
        ]);

        $responseHandler = new JsonExceptionResponseHandler($responseFactory, true);

        self::assertSame($response, $responseHandler->createExceptionResponse($request, $exception));
    }
}
