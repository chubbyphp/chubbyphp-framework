<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Integration;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Router\Exceptions\RouterException;
use Http\Factory\Guzzle\ResponseFactory as GuzzleResponseFactory;
use Http\Factory\Guzzle\ServerRequestFactory as GuzzleServerRequestFactory;
use Laminas\Diactoros\ResponseFactory as LaminasResponseFactory;
use Laminas\Diactoros\ServerRequestFactory as LaminasServerRequestFactory;
use Nyholm\Psr7\Factory\Psr17Factory as NyholmResponseFactory;
use Nyholm\Psr7\Factory\Psr17Factory as NyholmServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory as SlimResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory as SlimServerRequestFactory;
use Sunrise\Http\Message\ResponseFactory as SunriseResponseFactory;
use Sunrise\Http\Message\ServerRequestFactory as SunriseServerRequestFactory;

/**
 * @coversNothing
 *
 * @internal
 */
final class RouteMatcherLessTest extends TestCase
{
    public function providePsr7Implementations(): iterable
    {
        return [
            'guzzle' => [
                'responseFactory' => new GuzzleResponseFactory(),
                'serverRequestFactory' => new GuzzleServerRequestFactory(),
            ],
            'nyholm' => [
                'responseFactory' => new NyholmResponseFactory(),
                'serverRequestFactory' => new NyholmServerRequestFactory(),
            ],
            'slim' => [
                'responseFactory' => new SlimResponseFactory(),
                'serverRequestFactory' => new SlimServerRequestFactory(),
            ],
            'sunrise' => [
                'responseFactory' => new SunriseResponseFactory(),
                'serverRequestFactory' => new SunriseServerRequestFactory(),
            ],
            'zend' => [
                'responseFactory' => new LaminasResponseFactory(),
                'serverRequestFactory' => new LaminasServerRequestFactory(),
            ],
        ];
    }

    /**
     * @dataProvider providePsr7Implementations
     */
    public function testMissingRouteMatcherMiddleware(
        ResponseFactoryInterface $responseFactory,
        ServerRequestFactoryInterface $serverRequestFactory
    ): void {
        $app = new Application([
            new ExceptionMiddleware($responseFactory, true),
        ]);

        $request = $serverRequestFactory->createServerRequest('GET', '/hello/test');

        $response = $app->handle($request);

        self::assertSame(500, $response->getStatusCode());

        $body = (string) $response->getBody();

        self::assertStringContainsString(
            'Request attribute "route" missing or wrong type "null"'
                .', please add the "Chubbyphp\Framework\Middleware\RouteMatcherMiddleware" middleware',
            $body
        );
    }

    /**
     * @dataProvider providePsr7Implementations
     */
    public function testMissingRouteMatcherMiddlewareWithoutExceptionMiddleware(
        ResponseFactoryInterface $responseFactory,
        ServerRequestFactoryInterface $serverRequestFactory
    ): void {
        $this->expectException(RouterException::class);
        $this->expectExceptionMessage(
            'Request attribute "route" missing or wrong type "null"'
                .', please add the "Chubbyphp\Framework\Middleware\RouteMatcherMiddleware" middleware'
        );

        $app = new Application([]);

        $request = $serverRequestFactory->createServerRequest('GET', '/hello/test');

        $app->handle($request);
    }
}
