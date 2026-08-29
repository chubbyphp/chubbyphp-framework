<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Facade;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\Middleware\UrlGeneratorMiddleware;
use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\GroupInterface;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Framework\Router\RoutesByName;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface;

/**
 * A facade around Application / ExceptionMiddleware / RouteMatcherMiddleware / Route / Group: pure construction
 * sugar, the built application is the very same middleware pipe as with the explicit composition: exception
 * middleware first, app middlewares, route matcher middleware last.
 *
 * The facade is agnostic to the router implementation: the route matcher factory is given as a parameter,
 * for example `static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes)` from
 * `chubbyphp/chubbyphp-framework-router-fastroute`.
 *
 * The application builder is immutable: every route / group call returns a new application builder, so use
 * the return value (chaining or reassignment). Middlewares are given as an optional parameter directly
 * before the element content they wrap (the request handler / the group configure callback), and can be
 * omitted entirely if there are none. Route names are given as the required second parameter. Routes and
 * groups accept pathOptions as an optional last parameter, group pathOptions are merged into their children.
 * Beside the seven method shortcuts (delete / get / head / options / patch / post / put) there is a generic
 * `route` accepting any method as its first parameter: `->route('TRACE', '/trace', 'trace', $handler)`.
 *
 * With the createUrlGenerator option (for example
 * `static fn (RoutesByNameInterface $routes) => new UrlGenerator($routes)`) an `urlGenerator` request
 * attribute becomes available within middlewares and request handlers:
 * `$request->getAttribute(UrlGeneratorMiddleware::ATTRIBUTE)->generatePath('pet_read', ['id' => '1'])`.
 *
 * ```php
 * $createRouteMatcher = static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes);
 *
 * $app = ApplicationBuilder::create($responseFactory, $createRouteMatcher, [$corsMiddleware])
 *     ->get('/ping', 'ping', $pingHandler)
 *     ->get('/openapi', 'openapi', $openApiHandler)
 *     ->group(
 *         '/api/pets',
 *         [$acceptNegotiationMiddleware, $apiErrorMiddleware],
 *         static fn (GroupCollector $pets) => $pets
 *             ->get('', 'pet_list', $petListHandler)
 *             ->post('', 'pet_create', [$contentTypeNegotiationMiddleware], $petCreateHandler)
 *             ->get('/{id}', 'pet_read', $petReadHandler)
 *             ->put('/{id}', 'pet_update', [$contentTypeNegotiationMiddleware], $petUpdateHandler)
 *             ->delete('/{id}', 'pet_delete', $petDeleteHandler),
 *     )
 *     ->build();
 * ```
 */
final class ApplicationBuilder extends AbstractCollector
{
    /**
     * @param callable(RoutesByNameInterface): RouteMatcherInterface      $createRouteMatcher
     * @param array<MiddlewareInterface>                                  $middlewares
     * @param null|callable(RoutesByNameInterface): UrlGeneratorInterface $createUrlGenerator
     * @param list<GroupInterface|RouteInterface>                         $children
     */
    private function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly mixed $createRouteMatcher,
        private readonly array $middlewares,
        private readonly mixed $createUrlGenerator,
        private readonly bool $debug,
        private readonly ?LoggerInterface $logger,
        array $children
    ) {
        parent::__construct($children);
    }

    /**
     * @param callable(RoutesByNameInterface): RouteMatcherInterface      $createRouteMatcher
     * @param array<MiddlewareInterface>                                  $middlewares
     * @param null|callable(RoutesByNameInterface): UrlGeneratorInterface $createUrlGenerator
     */
    public static function create(
        ResponseFactoryInterface $responseFactory,
        callable $createRouteMatcher,
        array $middlewares = [],
        ?callable $createUrlGenerator = null,
        bool $debug = false,
        ?LoggerInterface $logger = null
    ): self {
        return new self($responseFactory, $createRouteMatcher, $middlewares, $createUrlGenerator, $debug, $logger, []);
    }

    public function build(): Application
    {
        $routesByName = new RoutesByName(Group::create('', $this->children)->getRoutes());

        $urlGenerator = null !== $this->createUrlGenerator ? ($this->createUrlGenerator)($routesByName) : null;

        return new Application([
            new ExceptionMiddleware($this->responseFactory, $this->debug, $this->logger),
            ...(null !== $urlGenerator ? [new UrlGeneratorMiddleware($urlGenerator)] : []),
            ...$this->middlewares,
            new RouteMatcherMiddleware(($this->createRouteMatcher)($routesByName)),
        ]);
    }

    /**
     * @param list<GroupInterface|RouteInterface> $children
     */
    protected function withChildren(array $children): static
    {
        return new self(
            $this->responseFactory,
            $this->createRouteMatcher,
            $this->middlewares,
            $this->createUrlGenerator,
            $this->debug,
            $this->logger,
            $children
        );
    }
}
