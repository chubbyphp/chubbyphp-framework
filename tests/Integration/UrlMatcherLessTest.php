<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Integration;

use Bitty\Http\ResponseFactory as BittyResponseFactory;
use Bitty\Http\ServerRequestFactory as BittyServerRequestFactory;
use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Router\RouterException;
use Fig\Http\Message\RequestMethodInterface as RequestMethod;
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
use Sunrise\Http\ServerRequest\ServerRequestFactory as SunriseServerRequestFactory;

/**
 * @coversNothing
 *
 * @internal
 */
final class UrlMatcherLessTest extends TestCase
{
    public function providePsr7Implementations(): array
    {
        return [
            'bitty' => [
                'responseFactory' => new BittyResponseFactory(),
                'serverRequestFactory' => new BittyServerRequestFactory(),
            ],
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
    public function testMissingUrlMatcherMiddleware(
        ResponseFactoryInterface $responseFactory,
        ServerRequestFactoryInterface $serverRequestFactory
    ): void {
        $app = new Application([
            new ExceptionMiddleware($responseFactory, true),
        ]);

        $request = $serverRequestFactory->createServerRequest(RequestMethod::METHOD_GET, '/hello/test');

        $response = $app->handle($request);

        self::assertSame(500, $response->getStatusCode());

        $body = (string) $response->getBody();

        self::assertStringContainsString(
            'Request attribute "route" missing or wrong type "NULL"'
                .', please add the "Chubbyphp\Framework\Middleware\UrlMatcherMiddleware" middleware',
            $body
        );
    }

    /**
     * @dataProvider providePsr7Implementations
     */
    public function testMissingUrlMatcherMiddlewareWithoutExceptionMiddleware(
        ResponseFactoryInterface $responseFactory,
        ServerRequestFactoryInterface $serverRequestFactory
    ): void {
        $this->expectException(RouterException::class);
        $this->expectExceptionMessage(
            'Request attribute "route" missing or wrong type "NULL"'
                .', please add the "Chubbyphp\Framework\Middleware\UrlMatcherMiddleware" middleware'
        );

        $app = new Application([]);

        $request = $serverRequestFactory->createServerRequest(RequestMethod::METHOD_GET, '/hello/test');

        $app->handle($request);
    }
}
