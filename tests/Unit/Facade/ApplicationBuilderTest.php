<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Facade;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Facade\GroupCollector;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \Chubbyphp\Framework\Facade\AbstractCollector
 * @covers \Chubbyphp\Framework\Facade\ApplicationBuilder
 *
 * @internal
 */
final class ApplicationBuilderTest extends TestCase
{
    public function testIsImmutable(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        $routeMatcherCalls = [];

        $createRouteMatcher = static function (RoutesByNameInterface $routesByName) use (
            $builder,
            &$routeMatcherCalls
        ): RouteMatcherInterface {
            $routeMatcherCalls[] = array_keys($routesByName->getRoutesByName());

            /** @var RouteMatcherInterface $routeMatcher */
            $routeMatcher = $builder->create(RouteMatcherInterface::class, []);

            return $routeMatcher;
        };

        $applicationBuilder = ApplicationBuilder::create($responseFactory, $createRouteMatcher);
        $applicationBuilderWithRoute = $applicationBuilder->get('/ping', 'ping', $handler);

        self::assertNotSame($applicationBuilder, $applicationBuilderWithRoute);

        self::assertInstanceOf(Application::class, $applicationBuilder->build());
        self::assertInstanceOf(Application::class, $applicationBuilderWithRoute->build());

        self::assertSame([[], ['ping']], $routeMatcherCalls);
    }

    public function testBuildMinimal(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $route = null;

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithCallback('withAttribute', static function (string $name, mixed $value) use (&$route, &$request) {
                self::assertSame('route', $name);
                self::assertSame($route, $value);

                return $request;
            }),
            new WithCallback('getAttribute', static function (string $name, mixed $default) use (&$route) {
                self::assertSame('route', $name);
                self::assertNull($default);

                return $route;
            }),
        ]);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var RouteInterface $route */
        $route = $builder->create(RouteInterface::class, [
            new WithReturn('getAttributes', [], []),
            new WithReturn('getMiddlewares', [], []),
            new WithReturn('getRequestHandler', [], $handler),
        ]);

        /** @var RouteMatcherInterface $routeMatcher */
        $routeMatcher = $builder->create(RouteMatcherInterface::class, [
            new WithReturn('match', [$request], $route),
        ]);

        $createRouteMatcher = static function (RoutesByNameInterface $routesByName) use ($routeMatcher, $handler) {
            $routesByName = $routesByName->getRoutesByName();

            self::assertSame(['ping'], array_keys($routesByName));
            self::assertSame('GET', $routesByName['ping']->getMethod());
            self::assertSame('/ping', $routesByName['ping']->getPath());
            self::assertSame([], $routesByName['ping']->getMiddlewares());
            self::assertSame([], $routesByName['ping']->getPathOptions());
            self::assertSame($handler, $routesByName['ping']->getRequestHandler());

            return $routeMatcher;
        };

        $application = ApplicationBuilder::create($responseFactory, $createRouteMatcher)
            ->get('/ping', 'ping', $handler)
            ->build()
        ;

        self::assertSame($response, $application->handle($request));
    }

    public function testBuildMaximal(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, []);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $builder->create(UrlGeneratorInterface::class, []);

        /** @var MiddlewareInterface $appMiddleware */
        $appMiddleware = $builder->create(MiddlewareInterface::class, [
            new WithCallback(
                'process',
                static fn (
                    ServerRequestInterface $request,
                    RequestHandlerInterface $requestHandler
                ) => $requestHandler->handle($request)
            ),
        ]);

        /** @var MiddlewareInterface $groupMiddleware */
        $groupMiddleware = $builder->create(MiddlewareInterface::class, [
            new WithCallback(
                'process',
                static fn (
                    ServerRequestInterface $request,
                    RequestHandlerInterface $requestHandler
                ) => $requestHandler->handle($request)
            ),
        ]);

        /** @var MiddlewareInterface $routeMiddleware */
        $routeMiddleware = $builder->create(MiddlewareInterface::class, [
            new WithCallback(
                'process',
                static fn (
                    ServerRequestInterface $request,
                    RequestHandlerInterface $requestHandler
                ) => $requestHandler->handle($request)
            ),
        ]);

        $route = null;

        $withAttributeCalls = [];

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithCallback('withAttribute', static function (string $name, mixed $value) use (
                &$withAttributeCalls,
                &$request
            ) {
                $withAttributeCalls[] = [$name, $value];

                return $request;
            }),
            new WithCallback('withAttribute', static function (string $name, mixed $value) use (
                &$withAttributeCalls,
                &$request
            ) {
                $withAttributeCalls[] = [$name, $value];

                return $request;
            }),
            new WithCallback('withAttribute', static function (string $name, mixed $value) use (
                &$withAttributeCalls,
                &$request
            ) {
                $withAttributeCalls[] = [$name, $value];

                return $request;
            }),
            new WithCallback('getAttribute', static function (string $name, mixed $default) use (&$route) {
                self::assertSame('route', $name);
                self::assertNull($default);

                return $route;
            }),
        ]);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var RouteInterface $route */
        $route = $builder->create(RouteInterface::class, [
            new WithReturn('getAttributes', [], ['id' => '1']),
            new WithReturn('getMiddlewares', [], [$groupMiddleware, $routeMiddleware]),
            new WithReturn('getRequestHandler', [], $handler),
        ]);

        /** @var RouteMatcherInterface $routeMatcher */
        $routeMatcher = $builder->create(RouteMatcherInterface::class, [
            new WithReturn('match', [$request], $route),
        ]);

        $expectRoutesByName = static function (RoutesByNameInterface $routesByName) use (
            $handler,
            $groupMiddleware,
            $routeMiddleware
        ): void {
            $routesByName = $routesByName->getRoutesByName();

            self::assertSame(['pet_list', 'pet_read'], array_keys($routesByName));

            self::assertSame('GET', $routesByName['pet_list']->getMethod());
            self::assertSame('/api/pets', $routesByName['pet_list']->getPath());
            self::assertSame([$groupMiddleware], $routesByName['pet_list']->getMiddlewares());
            self::assertSame(['tokens' => ['version' => '\d+']], $routesByName['pet_list']->getPathOptions());
            self::assertSame($handler, $routesByName['pet_list']->getRequestHandler());

            self::assertSame('GET', $routesByName['pet_read']->getMethod());
            self::assertSame('/api/pets/{id}', $routesByName['pet_read']->getPath());
            self::assertSame([$groupMiddleware, $routeMiddleware], $routesByName['pet_read']->getMiddlewares());
            self::assertSame(
                ['tokens' => ['version' => '\d+', 'id' => '\d+']],
                $routesByName['pet_read']->getPathOptions()
            );
            self::assertSame($handler, $routesByName['pet_read']->getRequestHandler());
        };

        $createRouteMatcher = static function (RoutesByNameInterface $routesByName) use (
            $expectRoutesByName,
            $routeMatcher
        ): RouteMatcherInterface {
            $expectRoutesByName($routesByName);

            return $routeMatcher;
        };

        $createUrlGenerator = static function (RoutesByNameInterface $routesByName) use (
            $expectRoutesByName,
            $urlGenerator
        ): UrlGeneratorInterface {
            $expectRoutesByName($routesByName);

            return $urlGenerator;
        };

        $application = ApplicationBuilder::create(
            $responseFactory,
            $createRouteMatcher,
            [$appMiddleware],
            $createUrlGenerator,
            true,
            $logger
        )
            ->group(
                '/api/pets',
                [$groupMiddleware],
                static fn (GroupCollector $pets) => $pets
                    ->get('', 'pet_list', $handler)
                    ->get('/{id}', 'pet_read', [$routeMiddleware], $handler, ['tokens' => ['id' => '\d+']]),
                ['tokens' => ['version' => '\d+']]
            )
            ->build()
        ;

        self::assertSame($response, $application->handle($request));

        self::assertSame(
            [['urlGenerator', $urlGenerator], ['route', $route], ['id', '1']],
            $withAttributeCalls
        );
    }

    public function testBuildWithExceptionMiddleware(): void
    {
        $builder = new MockObjectBuilder();

        /** @var StreamInterface $body */
        $body = $builder->create(StreamInterface::class, [
            new WithCallback('write', static function (string $html): int {
                self::assertStringContainsString('<title>Not Found</title>', $html);
                self::assertStringContainsString(
                    '<p>The path "/unknown" you are looking for could not be found.</p>',
                    $html
                );
                self::assertStringContainsString('Chubbyphp\HttpException\HttpException', $html);

                return \strlen($html);
            }),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Content-Type', 'text/html']),
            new WithReturn('getBody', [], $body),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [404, ''], $response),
        ]);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithCallback('info', static function (string $message, array $context): void {
                self::assertSame('Http Exception', $message);
                self::assertSame(404, $context['data']['status']);
            }),
        ]);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var RouteMatcherInterface $routeMatcher */
        $routeMatcher = $builder->create(RouteMatcherInterface::class, [
            new WithCallback('match', static function (ServerRequestInterface $request): never {
                throw HttpException::createNotFound([
                    'detail' => 'The path "/unknown" you are looking for could not be found.',
                ]);
            }),
        ]);

        $application = ApplicationBuilder::create(
            $responseFactory,
            static fn (RoutesByNameInterface $routesByName) => $routeMatcher,
            [],
            null,
            true,
            $logger
        )->build();

        self::assertSame($response, $application->handle($request));
    }

    public function testBuildWithExceptionMiddlewareDefaults(): void
    {
        $builder = new MockObjectBuilder();

        /** @var StreamInterface $body */
        $body = $builder->create(StreamInterface::class, [
            new WithCallback('write', static function (string $html): int {
                self::assertStringContainsString('<title>Not Found</title>', $html);
                self::assertStringNotContainsString('Chubbyphp\HttpException\HttpException', $html);

                return \strlen($html);
            }),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Content-Type', 'text/html']),
            new WithReturn('getBody', [], $body),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [404, ''], $response),
        ]);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        /** @var RouteMatcherInterface $routeMatcher */
        $routeMatcher = $builder->create(RouteMatcherInterface::class, [
            new WithCallback('match', static function (ServerRequestInterface $request): never {
                throw HttpException::createNotFound();
            }),
        ]);

        $application = ApplicationBuilder::create(
            $responseFactory,
            static fn (RoutesByNameInterface $routesByName) => $routeMatcher,
        )->build();

        self::assertSame($response, $application->handle($request));
    }
}
