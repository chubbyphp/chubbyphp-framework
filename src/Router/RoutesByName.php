<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

final class RoutesByName implements RoutesByNameInterface
{
    /**
     * @var array<string, RouteInterface>
     */
    private readonly array $routesByName;

    /**
     * @param array<RouteInterface> $routes
     */
    public function __construct(array $routes)
    {
        $routesByName = [];
        foreach ($routes as $route) {
            $routesByName[$route->getName()] = $route;
        }

        $this->routesByName = $routesByName;
    }

    /**
     * @return array<string, RouteInterface>
     */
    public function getRoutesByName(): array
    {
        return $this->routesByName;
    }
}
