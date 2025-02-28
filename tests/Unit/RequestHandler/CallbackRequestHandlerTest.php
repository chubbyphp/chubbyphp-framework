<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\RequestHandler;

use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Framework\RequestHandler\CallbackRequestHandler
 *
 * @internal
 */
final class CallbackRequestHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        $requestHandler = new CallbackRequestHandler(static fn (ServerRequestInterface $request) => $response);

        self::assertSame($response, $requestHandler->handle($request));
    }
}
