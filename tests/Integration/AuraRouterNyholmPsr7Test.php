<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Integration;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\AuraRouter;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouterException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @coversNothing
 *
 * @internal
 */
final class AuraRouterNyholmPsr7Test extends TestCase
{
    public function testOk(): void
    {
        $psr17Factory = new Psr17Factory();

        $route = Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($psr17Factory) {
                $name = $request->getAttribute('name');
                $response = $psr17Factory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ))->pathOptions(['tokens' => ['name' => '[a-z]+']]);

        $app = new Application([
            new ExceptionMiddleware($psr17Factory, true),
            new RouterMiddleware(new AuraRouter([$route]), $psr17Factory),
        ]);

        $request = new ServerRequest(
            RouteInterface::GET,
            '/hello/test'
        );

        $response = $app->handle($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('Hello, test', (string) $response->getBody());
    }

    public function testTestNotFound(): void
    {
        $psr17Factory = new Psr17Factory();

        $route = Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($psr17Factory) {
                $name = $request->getAttribute('name');
                $response = $psr17Factory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ))->pathOptions(['tokens' => ['name' => '[a-z]+']]);

        $app = new Application([
            new ExceptionMiddleware($psr17Factory, true),
            new RouterMiddleware(new AuraRouter([$route]), $psr17Factory),
        ]);

        $request = new ServerRequest(
            RouteInterface::GET,
            '/hello'
        );

        $response = $app->handle($request);

        self::assertSame(404, $response->getStatusCode());
        self::assertStringContainsString(
            'The page "/hello" you are looking for could not be found.',
            (string) $response->getBody()
        );
    }

    public function testMethodNotAllowed(): void
    {
        $psr17Factory = new Psr17Factory();

        $route = Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($psr17Factory) {
                $name = $request->getAttribute('name');
                $response = $psr17Factory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ))->pathOptions(['tokens' => ['name' => '[a-z]+']]);

        $app = new Application([
            new ExceptionMiddleware($psr17Factory, true),
            new RouterMiddleware(new AuraRouter([$route]), $psr17Factory),
        ]);

        $request = new ServerRequest(
            RouteInterface::POST,
            '/hello/test'
        );

        $response = $app->handle($request);

        self::assertSame(405, $response->getStatusCode());
        self::assertStringContainsString(
            'Method "POST" at path "/hello/test" is not allowed.',
            (string) $response->getBody()
        );
    }

    public function testException(): void
    {
        $psr17Factory = new Psr17Factory();

        $route = Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
            function (): void {
                throw new \RuntimeException('Something went wrong');
            }
        ))->pathOptions(['tokens' => ['name' => '[a-z]+']]);

        $app = new Application([
            new ExceptionMiddleware($psr17Factory, true),
            new RouterMiddleware(new AuraRouter([$route]), $psr17Factory),
        ]);

        $request = new ServerRequest(
            RouteInterface::GET,
            '/hello/test'
        );

        $response = $app->handle($request);

        self::assertSame(500, $response->getStatusCode());

        $body = (string) $response->getBody();

        self::assertStringContainsString('RuntimeException', $body);
        self::assertStringContainsString('Something went wrong', $body);
    }

    public function testExceptionWithoutExceptionMiddleware(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Something went wrong');

        $psr17Factory = new Psr17Factory();

        $route = Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
            function (): void {
                throw new \RuntimeException('Something went wrong');
            }
        ))->pathOptions(['tokens' => ['name' => '[a-z]+']]);

        $app = new Application([
            new RouterMiddleware(new AuraRouter([$route]), $psr17Factory),
        ]);

        $request = new ServerRequest(
            RouteInterface::GET,
            '/hello/test'
        );

        $app->handle($request);
    }

    public function testMissingRouterMiddleware(): void
    {
        $psr17Factory = new Psr17Factory();

        $app = new Application([
            new ExceptionMiddleware($psr17Factory, true),
        ]);

        $request = new ServerRequest(
            RouteInterface::GET,
            '/hello/test'
        );

        $response = $app->handle($request);

        self::assertSame(500, $response->getStatusCode());

        $body = (string) $response->getBody();

        self::assertStringContainsString(
            'Request attribute "route" missing or wrong type "NULL"'
                .', please add the "Chubbyphp\Framework\Middleware\RouterMiddleware" middleware',
            $body
        );
    }

    public function testMissingRouterMiddlewareWithoutExceptionMiddleware(): void
    {
        $this->expectException(RouterException::class);
        $this->expectExceptionMessage(
            'Request attribute "route" missing or wrong type "NULL"'
                .', please add the "Chubbyphp\Framework\Middleware\RouterMiddleware" middleware'
        );

        $app = new Application([]);

        $request = new ServerRequest(
            RouteInterface::GET,
            '/hello/test'
        );

        $app->handle($request);
    }
}
