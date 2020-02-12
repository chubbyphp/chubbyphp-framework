<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Aura\Router\Generator;
use Aura\Router\Matcher;
use Aura\Router\RouterContainer;
use Aura\Router\Rule\Allows;
use Chubbyphp\Framework\Router\Exceptions\MethodNotAllowedException;
use Chubbyphp\Framework\Router\Exceptions\MissingRouteByNameException;
use Chubbyphp\Framework\Router\Exceptions\NotFoundException;
use Psr\Http\Message\ServerRequestInterface;

final class AuraRouter implements RouterInterface
{
    public const PATH_DEFAULTS = 'defaults';
    public const PATH_HOST = 'host';
    public const PATH_SECURE = 'secure';
    public const PATH_SPECIAL = 'special';
    public const PATH_TOKENS = 'tokens';
    public const PATH_WILDCARD = 'wildcard';

    /**
     * @var array<string, RouteInterface>
     */
    private $routes = [];

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var Matcher
     */
    private $matcher;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @param array<int, RouteInterface> $routes
     */
    public function __construct(array $routes, string $basePath = '')
    {
        $this->routes = $this->getRoutesByName($routes);

        $routerContainer = $this->getRouterContainer($routes);

        $this->generator = $routerContainer->getGenerator();
        $this->matcher = $routerContainer->getMatcher();
        $this->basePath = $basePath;
    }

    public function match(ServerRequestInterface $request): RouteInterface
    {
        if (!$auraRoute = $this->matcher->match($request)) {
            $failedAuraRoute = $this->matcher->getFailedRoute();
            switch ($failedAuraRoute->failedRule) {
                case Allows::class:
                    throw MethodNotAllowedException::create(
                        $request->getRequestTarget(),
                        $request->getMethod(),
                        $failedAuraRoute->allows
                    );
                default:
                    throw NotFoundException::create($request->getRequestTarget());
            }
        }

        /** @var RouteInterface $route */
        $route = $this->routes[$auraRoute->name];

        return $route->withAttributes($auraRoute->attributes);
    }

    /**
     * @param array<string, string> $attributes
     * @param array<string, mixed>  $queryParams
     *
     * @throws RouterException
     */
    public function generateUrl(
        ServerRequestInterface $request,
        string $name,
        array $attributes = [],
        array $queryParams = []
    ): string {
        $uri = $request->getUri();
        $requestTarget = $this->generatePath($name, $attributes, $queryParams);

        return $uri->getScheme().'://'.$uri->getAuthority().$requestTarget;
    }

    /**
     * @param array<string, string> $attributes
     * @param array<string, mixed>  $queryParams
     *
     * @throws RouterException
     */
    public function generatePath(string $name, array $attributes = [], array $queryParams = []): string
    {
        if (!isset($this->routes[$name])) {
            throw MissingRouteByNameException::create($name);
        }

        $path = $this->generator->generate($name, $attributes);

        if ([] === $queryParams) {
            return $this->basePath.$path;
        }

        return $this->basePath.$path.'?'.http_build_query($queryParams);
    }

    /**
     * @param array<int, RouteInterface> $routes
     *
     * @return array<string, RouteInterface>
     */
    private function getRoutesByName(array $routes): array
    {
        $routesByName = [];
        foreach ($routes as $route) {
            $routesByName[$route->getName()] = $route;
        }

        return $routesByName;
    }

    /**
     * @param array<int, RouteInterface> $routes
     */
    private function getRouterContainer(array $routes): RouterContainer
    {
        $routerContainer = new RouterContainer();

        $map = $routerContainer->getMap();

        foreach ($routes as $route) {
            $options = $route->getPathOptions();

            $auraRoute = $map->route($route->getName(), $route->getPath());
            $auraRoute->allows($route->getMethod());

            $auraRoute->defaults($options[self::PATH_DEFAULTS] ?? []);
            $auraRoute->host($options[self::PATH_HOST] ?? null);
            $auraRoute->secure($options[self::PATH_SECURE] ?? null);
            $auraRoute->special($options[self::PATH_SPECIAL] ?? null);
            $auraRoute->tokens($options[self::PATH_TOKENS] ?? []);
            $auraRoute->wildcard($options[self::PATH_WILDCARD] ?? null);
        }

        return $routerContainer;
    }
}
