<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\Generator;
use Aura\Router\Matcher;
use Aura\Router\RouterContainer;
use Aura\Router\Rule\Allows;
use Psr\Http\Message\ServerRequestInterface;

final class AuraRouter implements RouterInterface
{
    /**
     * @var array<RouteInterface>
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
     * @param array<RouteInterface> $routes
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
                    throw RouterException::createForMethodNotAllowed(
                        $request->getMethod(),
                        $failedAuraRoute->allows,
                        $request->getRequestTarget()
                    );
                default:
                    throw RouterException::createForNotFound($request->getRequestTarget());
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
            throw RouterException::createForMissingRoute($name);
        }

        $path = $this->generator->generate($name, $attributes);

        if ([] === $queryParams) {
            return $this->basePath.$path;
        }

        return $this->basePath.$path.'?'.http_build_query($queryParams);
    }

    /**
     * @param array<RouteInterface> $routes
     *
     * @return array<RouteInterface>
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
     * @param array<RouteInterface> $routes
     */
    private function getRouterContainer(array $routes): RouterContainer
    {
        $routerContainer = new RouterContainer();

        $map = $routerContainer->getMap();

        foreach ($routes as $route) {
            $options = $route->getPathOptions();

            $auraRoute = $map->route($route->getName(), $route->getPath());
            $auraRoute->allows($route->getMethod());

            $auraRoute->defaults($options['defaults'] ?? []);
            $auraRoute->host($options['host'] ?? null);
            $auraRoute->secure($options['secure'] ?? null);
            $auraRoute->special($options['special'] ?? null);
            $auraRoute->tokens($options['tokens'] ?? []);
            $auraRoute->wildcard($options['wildcard'] ?? null);
        }

        return $routerContainer;
    }
}
