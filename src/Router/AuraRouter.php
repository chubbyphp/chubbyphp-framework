<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\Generator;
use Aura\Router\Matcher;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ServerRequestInterface;
use Aura\Router\Rule\Allows;

final class AuraRouter implements RouterInterface
{
    /**
     * @var RouteInterface[]
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
     * @param RouteInterface[] $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $this->getRoutesByName($routes);

        $routerContainer = $this->getRouterContainer($routes);

        $this->generator = $routerContainer->getGenerator();
        $this->matcher = $routerContainer->getMatcher();
    }

    /**
     * @param RouteInterface[] $routes
     *
     * @return RouteInterface[]
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
     * @param RouteInterface[] $routes
     *
     * @return RouterContainer
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

    /**
     * @param ServerRequestInterface $request
     *
     * @return RouteInterface
     */
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
        $route = $route->withAttributes($auraRoute->attributes);

        return $route;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string                 $name
     * @param string[]               $attributes
     * @param array                  $queryParams
     *
     * @return string
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
     * @param string   $name
     * @param string[] $attributes
     * @param array    $queryParams
     *
     * @return string
     *
     * @throws RouterException
     */
    public function generatePath(string $name, array $attributes = [], array $queryParams = []): string
    {
        try {
            $path = $this->generator->generate($name, $attributes);

            if ([] === $queryParams) {
                return $path;
            }

            return $path.'?'.http_build_query($queryParams);
        } catch (RouteNotFound $exception) {
            throw RouterException::createForMissingRoute($name);
        }
    }
}
