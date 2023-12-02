<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Integration;

use Chubbyphp\Framework\Middleware\CallbackMiddleware;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\Middleware\SlimCallbackMiddleware;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\RequestHandler\SlimCallbackRequestHandler;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory as SlimResponseFactory;
use Sunrise\Http\Message\ServerRequestFactory as SunriseServerRequestFactory;

/**
 * @coversNothing
 *
 * @internal
 */
final class MiddlewareDispatcherTest extends TestCase
{
    use MockByCallsTrait;

    public function testCallback(): void
    {
        $responseFactory = new SlimResponseFactory();
        $serverRequestFactory = new SunriseServerRequestFactory();

        $middlewareDispatcher = new MiddlewareDispatcher();
        $middlewareDispatcher->dispatch(
            [
                new CallbackMiddleware(
                    static fn (ServerRequestInterface $request, RequestHandlerInterface $handler) => $handler->handle($request->withHeader('req1', 'value1'))
                ),
                new CallbackMiddleware(
                    static fn (ServerRequestInterface $request, RequestHandlerInterface $handler) => $handler->handle($request->withHeader('req2', 'value2'))
                ),
                new CallbackMiddleware(
                    static fn (ServerRequestInterface $request, RequestHandlerInterface $handler) => $handler->handle($request->withHeader('req3', 'value3'))
                ),
                new CallbackMiddleware(
                    static fn (ServerRequestInterface $request, RequestHandlerInterface $handler) => $handler->handle($request->withAttribute('req', 'value'))
                ),
            ],
            new CallbackRequestHandler(
                static function (ServerRequestInterface $request) use ($responseFactory) {
                    self::assertSame([
                        'req1' => ['value1'],
                        'req2' => ['value2'],
                        'req3' => ['value3'],
                    ], $request->getHeaders());

                    return $responseFactory->createResponse();
                }
            ),
            $serverRequestFactory->createServerRequest('GET', '/hello/test')
        );
    }

    public function testSlim(): void
    {
        $responseFactory = new SlimResponseFactory();
        $serverRequestFactory = new SunriseServerRequestFactory();

        $middlewareDispatcher = new MiddlewareDispatcher();
        $middlewareDispatcher->dispatch(
            [
                new SlimCallbackMiddleware(
                    static fn (ServerRequestInterface $req, ResponseInterface $res, callable $next) => $next($req->withHeader('req1', 'value1'), $res->withHeader('res1', 'value1')),
                    $responseFactory
                ),
                new SlimCallbackMiddleware(
                    static fn (ServerRequestInterface $req, ResponseInterface $res, callable $next) => $next($req->withHeader('req2', 'value2'), $res->withHeader('res2', 'value2')),
                    $responseFactory
                ),
                new SlimCallbackMiddleware(
                    static fn (ServerRequestInterface $req, ResponseInterface $res, callable $next) => $next($req->withHeader('req3', 'value3'), $res->withHeader('res3', 'value3')),
                    $responseFactory
                ),
                new SlimCallbackMiddleware(
                    static fn (ServerRequestInterface $req, ResponseInterface $res, callable $next) => $next($req->withAttribute('req', 'value'), $res),
                    $responseFactory
                ),
            ],
            new SlimCallbackRequestHandler(
                static function (ServerRequestInterface $req, ResponseInterface $res, array $args) {
                    self::assertSame([
                        'req1' => ['value1'],
                        'req2' => ['value2'],
                        'req3' => ['value3'],
                    ], $req->getHeaders());

                    self::assertSame([
                        'res1' => ['value1'],
                        'res2' => ['value2'],
                        'res3' => ['value3'],
                    ], $res->getHeaders());

                    self::assertSame(['response', 'req'], array_keys($args));
                    self::assertSame('value', $args['req']);

                    return $res;
                },
                $responseFactory
            ),
            $serverRequestFactory->createServerRequest('GET', '/hello/test')
        );
    }

    public function testCallbackSlimMixed(): void
    {
        $responseFactory = new SlimResponseFactory();
        $serverRequestFactory = new SunriseServerRequestFactory();

        $middlewareDispatcher = new MiddlewareDispatcher();
        $middlewareDispatcher->dispatch(
            [
                new CallbackMiddleware(
                    static fn (ServerRequestInterface $request, RequestHandlerInterface $handler) => $handler->handle($request->withHeader('req1', 'value1'))
                ),
                new SlimCallbackMiddleware(
                    static fn (ServerRequestInterface $req, ResponseInterface $res, callable $next) => $next($req->withHeader('req2', 'value2'), $res->withHeader('res2', 'value2')),
                    $responseFactory
                ),
                new CallbackMiddleware(
                    static fn (ServerRequestInterface $request, RequestHandlerInterface $handler) => $handler->handle($request->withHeader('req3', 'value3'))
                ),
                new SlimCallbackMiddleware(
                    static fn (ServerRequestInterface $req, ResponseInterface $res, callable $next) => $next($req->withAttribute('req', 'value'), $res),
                    $responseFactory
                ),
            ],
            new SlimCallbackRequestHandler(
                static function (ServerRequestInterface $req, ResponseInterface $res, array $args) {
                    self::assertSame([
                        'req1' => ['value1'],
                        'req2' => ['value2'],
                        'req3' => ['value3'],
                    ], $req->getHeaders());

                    self::assertSame([
                        'res2' => ['value2'],
                    ], $res->getHeaders());

                    self::assertSame(['response', 'req'], array_keys($args));
                    self::assertSame('value', $args['req']);

                    return $res;
                },
                $responseFactory
            ),
            $serverRequestFactory->createServerRequest('GET', '/hello/test')
        );
    }
}
