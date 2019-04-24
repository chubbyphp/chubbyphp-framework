<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\AuraRouter;

use Aura\Router\Matcher;
use Aura\Router\RouterContainer;
use Aura\Router\Rule\Allows;
use Chubbyphp\Framework\Router\RouteMatcherException;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RouteMatcher implements RouteMatcherInterface
{
    /**
     * @var RouteInterface[]
     */
    private $routes = [];

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
        $this->matcher = $this->getMatcher($routes);
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
     * @return Matcher
     */
    private function getMatcher(array $routes): Matcher
    {
        $routerContainer = new RouterContainer();

        $map = $routerContainer->getMap();

        foreach ($routes as $route) {
            $options = $route->getPathOptions();

            $auraRoute = $map->route($route->getName(), $route->getPath());
            $auraRoute->allows($route->getMethod());
            $auraRoute->tokens($options['tokens'] ?? []);
            $auraRoute->defaults($options['defaults'] ?? []);
        }

        return $routerContainer->getMatcher();
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
                    throw RouteMatcherException::createForMethodNotAllowed(
                        $request->getMethod(),
                        $failedAuraRoute->allows,
                        $request->getRequestTarget()
                    );
                default:
                    throw RouteMatcherException::createForNotFound($request->getRequestTarget());
            }
        }

        /** @var RouteInterface $route */
        $route = $this->routes[$auraRoute->name];
        $route = $route->withAttributes($auraRoute->attributes);

        return $route;
    }
}
