<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Integration;

use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Facade\GroupCollector;
use Chubbyphp\Framework\Middleware\CallbackMiddleware;
use Chubbyphp\Framework\Middleware\UrlGeneratorMiddleware;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\HttpException\HttpException;
use Http\Factory\Guzzle\ResponseFactory as GuzzleResponseFactory;
use Http\Factory\Guzzle\ServerRequestFactory as GuzzleServerRequestFactory;
use Laminas\Diactoros\ResponseFactory as LaminasResponseFactory;
use Laminas\Diactoros\ServerRequestFactory as LaminasServerRequestFactory;
use Nyholm\Psr7\Factory\Psr17Factory as NyholmResponseFactory;
use Nyholm\Psr7\Factory\Psr17Factory as NyholmServerRequestFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory as SlimResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory as SlimServerRequestFactory;
use Sunrise\Http\Message\ResponseFactory as SunriseResponseFactory;
use Sunrise\Http\Message\ServerRequestFactory as SunriseServerRequestFactory;

/**
 * @coversNothing
 *
 * @internal
 */
final class ApplicationBuilderTest extends TestCase
{
    #[DataProvider('provideUsageExampleCases')]
    public function testUsageExample(
        ResponseFactoryInterface $responseFactory,
        ServerRequestFactoryInterface $serverRequestFactory
    ): void {
        $middlewareLog = [];

        $createMiddleware = static function (string $name) use (&$middlewareLog): CallbackMiddleware {
            return new CallbackMiddleware(
                static function (ServerRequestInterface $request, RequestHandlerInterface $handler) use (
                    $name,
                    &$middlewareLog
                ): ResponseInterface {
                    $middlewareLog[] = $name;

                    return $handler->handle($request);
                }
            );
        };

        $json = static function (ResponseFactoryInterface $responseFactory, mixed $data, int $status = 200) {
            $response = $responseFactory->createResponse($status)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR));

            return $response;
        };

        $pingHandler = new CallbackRequestHandler(
            static fn () => $json($responseFactory, ['datetime' => '2026-08-14T12:00:00.000Z'])
        );

        $petListHandler = new CallbackRequestHandler(
            static fn () => $json($responseFactory, [['id' => '1'], ['id' => '2']])
        );

        $petCreateHandler = new CallbackRequestHandler(
            static fn () => $json($responseFactory, ['id' => '1'], 201)
        );

        $petReadHandler = new CallbackRequestHandler(
            static function (ServerRequestInterface $request) use ($json, $responseFactory) {
                /** @var UrlGeneratorInterface $urlGenerator */
                $urlGenerator = $request->getAttribute(UrlGeneratorMiddleware::ATTRIBUTE);

                return $json($responseFactory, [
                    'id' => $request->getAttribute('id'),
                    'path' => $urlGenerator->generatePath('pet_read', ['id' => $request->getAttribute('id')]),
                ]);
            }
        );

        $petDeleteHandler = new CallbackRequestHandler(
            static fn () => $responseFactory->createResponse(204)
        );

        // a minimal router implementation, matching by method + path, resolving {id}
        $createRouteMatcher = static function (RoutesByNameInterface $routesByName): RouteMatcherInterface {
            return new class($routesByName) implements RouteMatcherInterface {
                public function __construct(private readonly RoutesByNameInterface $routesByName) {}

                public function match(ServerRequestInterface $request): RouteInterface
                {
                    $path = $request->getUri()->getPath();

                    foreach ($this->routesByName->getRoutesByName() as $route) {
                        if ($route->getMethod() !== $request->getMethod()) {
                            continue;
                        }

                        $pattern = '#^'.preg_replace('#\{([a-z]+)\}#', '(?P<$1>[^/]+)', $route->getPath()).'$#';

                        if (1 === preg_match($pattern, $path, $matches)) {
                            return $route->withAttributes(
                                array_filter($matches, static fn ($key) => \is_string($key), ARRAY_FILTER_USE_KEY)
                            );
                        }
                    }

                    throw HttpException::createNotFound([
                        'detail' => \sprintf('The path "%s" you are looking for could not be found.', $path),
                    ]);
                }
            };
        };

        $createUrlGenerator = static function (RoutesByNameInterface $routesByName): UrlGeneratorInterface {
            return new class($routesByName) implements UrlGeneratorInterface {
                public function __construct(private readonly RoutesByNameInterface $routesByName) {}

                public function generateUrl(
                    ServerRequestInterface $request,
                    string $name,
                    array $attributes = [],
                    array $queryParams = []
                ): string {
                    return 'https://example.com'.$this->generatePath($name, $attributes, $queryParams);
                }

                public function generatePath(string $name, array $attributes = [], array $queryParams = []): string
                {
                    $path = $this->routesByName->getRoutesByName()[$name]->getPath();

                    foreach ($attributes as $key => $value) {
                        $path = str_replace('{'.$key.'}', $value, $path);
                    }

                    return $path;
                }
            };
        };

        $app = ApplicationBuilder::create(
            $responseFactory,
            $createRouteMatcher,
            [$createMiddleware('cors')],
            $createUrlGenerator,
            true
        )
            ->get('/ping', 'ping', $pingHandler)
            ->group(
                '/api/pets',
                [$createMiddleware('acceptNegotiation'), $createMiddleware('apiError')],
                static fn (GroupCollector $pets) => $pets
                    ->get('', 'pet_list', $petListHandler)
                    ->post('', 'pet_create', [$createMiddleware('contentTypeNegotiation')], $petCreateHandler)
                    ->get('/{id}', 'pet_read', $petReadHandler)
                    ->delete('/{id}', 'pet_delete', $petDeleteHandler)
            )
            ->build()
        ;

        $response = $app->handle($serverRequestFactory->createServerRequest('GET', 'https://example.com/ping'));
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('{"datetime":"2026-08-14T12:00:00.000Z"}', (string) $response->getBody());
        self::assertSame(['cors'], $middlewareLog);

        $middlewareLog = [];
        $response = $app->handle($serverRequestFactory->createServerRequest('GET', 'https://example.com/api/pets'));
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('[{"id":"1"},{"id":"2"}]', (string) $response->getBody());
        self::assertSame(['cors', 'acceptNegotiation', 'apiError'], $middlewareLog);

        $middlewareLog = [];
        $response = $app->handle($serverRequestFactory->createServerRequest('POST', 'https://example.com/api/pets'));
        self::assertSame(201, $response->getStatusCode());
        self::assertSame('{"id":"1"}', (string) $response->getBody());
        self::assertSame(['cors', 'acceptNegotiation', 'apiError', 'contentTypeNegotiation'], $middlewareLog);

        $middlewareLog = [];
        $response = $app->handle($serverRequestFactory->createServerRequest('GET', 'https://example.com/api/pets/1'));
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('{"id":"1","path":"\/api\/pets\/1"}', (string) $response->getBody());
        self::assertSame(['cors', 'acceptNegotiation', 'apiError'], $middlewareLog);

        $middlewareLog = [];
        $response = $app->handle(
            $serverRequestFactory->createServerRequest('DELETE', 'https://example.com/api/pets/1')
        );
        self::assertSame(204, $response->getStatusCode());
        self::assertSame('', (string) $response->getBody());
        self::assertSame(['cors', 'acceptNegotiation', 'apiError'], $middlewareLog);

        $middlewareLog = [];
        $response = $app->handle($serverRequestFactory->createServerRequest('GET', 'https://example.com/unknown'));
        self::assertSame(404, $response->getStatusCode());
        self::assertStringContainsString('<title>Not Found</title>', (string) $response->getBody());
        self::assertSame(['cors'], $middlewareLog);
    }

    public static function provideUsageExampleCases(): iterable
    {
        return [
            'guzzle' => [
                'responseFactory' => new GuzzleResponseFactory(),
                'serverRequestFactory' => new GuzzleServerRequestFactory(),
            ],
            'laminas' => [
                'responseFactory' => new LaminasResponseFactory(),
                'serverRequestFactory' => new LaminasServerRequestFactory(),
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
        ];
    }
}
