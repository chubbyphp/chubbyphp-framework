<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

final class Routes implements RoutesInterface
{
    /**
     * @var array<string, RouteInterface>
     */
    private array $routes;

    /**
     * @param array<RouteInterface> $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $this->aggregateRoutesByName($routes);
    }

    /**
     * @return array<string, RouteInterface>
     */
    public function getRoutesByName(): array
    {
        return $this->routes;
    }

    /**
     * @param array<int, RouteInterface> $routes
     *
     * @return array<string, RouteInterface>
     */
    private function aggregateRoutesByName(array $routes): array
    {
        $routesByName = [];

        foreach ($routes as $i => $route) {
            if (!$route instanceof RouteInterface) {
                throw new \TypeError(
                    sprintf(
                        '%s::__construct() expects parameter 1 at index %d to be %s[], %s[] given',
                        self::class,
                        $i,
                        RouteInterface::class,
                        $route::class
                    )
                );
            }
            $routesByName[$route->getName()] = $route;
        }

        return $routesByName;
    }
}
