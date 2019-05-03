<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Integration;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ExceptionHandler;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequest;

/**
 * @coversNothing
 */
final class FastRouteTest extends TestCase
{
    public function testOk(): void
    {
        $responseFactory = new ResponseFactory();

        $route = Route::get('/hello/{name:[a-z]+}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($responseFactory) {
                $name = $request->getAttribute('name');
                $response = $responseFactory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ));

        $app = new Application(
            new FastRouteRouter([$route]),
            new MiddlewareDispatcher(),
            new ExceptionHandler($responseFactory, new NullLogger(), true)
        );

        $request = new ServerRequest(
            [],
            [],
            '/hello/test',
            RouteInterface::GET,
            'php://memory'
        );

        $response = $app->handle($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('Hello, test', (string) $response->getBody());
    }

    public function testTestNotFound(): void
    {
        $responseFactory = new ResponseFactory();

        $route = Route::get('/hello/{name:[a-z]+}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($responseFactory) {
                $name = $request->getAttribute('name');
                $response = $responseFactory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ));

        $app = new Application(
            new FastRouteRouter([$route]),
            new MiddlewareDispatcher(),
            new ExceptionHandler($responseFactory, new NullLogger(), true)
        );

        $request = new ServerRequest(
            [],
            [],
            '/hello',
            RouteInterface::GET,
            'php://memory'
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
        $responseFactory = new ResponseFactory();

        $route = Route::get('/hello/{name:[a-z]+}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($responseFactory) {
                $name = $request->getAttribute('name');
                $response = $responseFactory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ));

        $app = new Application(
            new FastRouteRouter([$route]),
            new MiddlewareDispatcher(),
            new ExceptionHandler($responseFactory, new NullLogger(), true)
        );

        $request = new ServerRequest(
            [],
            [],
            '/hello/test',
            RouteInterface::POST,
            'php://memory'
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
        $responseFactory = new ResponseFactory();

        $route = Route::get('/hello/{name:[a-z]+}', 'hello', new CallbackRequestHandler(
            function () {
                throw new \RuntimeException('Something went wrong');
            }
        ));

        $app = new Application(
            new FastRouteRouter([$route]),
            new MiddlewareDispatcher(),
            new ExceptionHandler($responseFactory, new NullLogger(), true)
        );

        $request = new ServerRequest(
            [],
            [],
            '/hello/test',
            RouteInterface::GET,
            'php://memory'
        );

        $response = $app->handle($request);

        self::assertSame(500, $response->getStatusCode());

        $body = (string) $response->getBody();

        self::assertStringContainsString('RuntimeException', $body);
        self::assertStringContainsString('Something went wrong', $body);
    }
}
