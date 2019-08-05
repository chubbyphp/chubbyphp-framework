<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Integration;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ExceptionHandler;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\AuraRouter;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;

/**
 * @coversNothing
 *
 * @internal
 */
final class AuraRouterSlimPsr7Test extends TestCase
{
    public function testOk(): void
    {
        $responseFactory = new ResponseFactory();

        $route = Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($responseFactory) {
                $name = $request->getAttribute('name');
                $response = $responseFactory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ))->pathOptions(['tokens' => ['name' => '[a-z]+']]);

        $app = new Application(
            new AuraRouter([$route]),
            new MiddlewareDispatcher(),
            new ExceptionHandler($responseFactory, true)
        );

        $request = new Request(
            RouteInterface::GET,
            (new UriFactory())->createUri('/hello/test'),
            new Headers(),
            [],
            [],
            (new StreamFactory())->createStreamFromFile('php://memory')
        );

        $response = $app->handle($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('Hello, test', (string) $response->getBody());
    }

    public function testTestNotFound(): void
    {
        $responseFactory = new ResponseFactory();

        $route = Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($responseFactory) {
                $name = $request->getAttribute('name');
                $response = $responseFactory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ))->pathOptions(['tokens' => ['name' => '[a-z]+']]);

        $app = new Application(
            new AuraRouter([$route]),
            new MiddlewareDispatcher(),
            new ExceptionHandler($responseFactory, true)
        );

        $request = new Request(
            RouteInterface::GET,
            (new UriFactory())->createUri('/hello'),
            new Headers(),
            [],
            [],
            (new StreamFactory())->createStreamFromFile('php://memory')
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

        $route = Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($responseFactory) {
                $name = $request->getAttribute('name');
                $response = $responseFactory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ))->pathOptions(['tokens' => ['name' => '[a-z]+']]);

        $app = new Application(
            new AuraRouter([$route]),
            new MiddlewareDispatcher(),
            new ExceptionHandler($responseFactory, true)
        );

        $request = new Request(
            RouteInterface::POST,
            (new UriFactory())->createUri('/hello/test'),
            new Headers(),
            [],
            [],
            (new StreamFactory())->createStreamFromFile('php://memory')
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

        $route = Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
            function (): void {
                throw new \RuntimeException('Something went wrong');
            }
        ))->pathOptions(['tokens' => ['name' => '[a-z]+']]);

        $app = new Application(
            new AuraRouter([$route]),
            new MiddlewareDispatcher(),
            new ExceptionHandler($responseFactory, true)
        );

        $request = new Request(
            RouteInterface::GET,
            (new UriFactory())->createUri('/hello/test'),
            new Headers(),
            [],
            [],
            (new StreamFactory())->createStreamFromFile('php://memory')
        );

        $response = $app->handle($request);

        self::assertSame(500, $response->getStatusCode());

        $body = (string) $response->getBody();

        self::assertStringContainsString('RuntimeException', $body);
        self::assertStringContainsString('Something went wrong', $body);
    }
}
