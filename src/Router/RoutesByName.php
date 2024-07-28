<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

final class RoutesByName implements RoutesByNameInterface
{
    /**
     * @var array<string, RouteInterface>
     */
    private array $routes = [];

    /**
     * @param array<RouteInterface> $routes
     */
    public function __construct(array $routes)
    {
        foreach ($routes as $i => $route) {
            if (!$route instanceof RouteInterface) {
                throw new \TypeError(
                    \sprintf(
                        '%s::__construct() expects parameter 1 at index %s to be %s, %s given',
                        self::class,
                        $i,
                        RouteInterface::class,
                        $route::class
                    )
                );
            }
            $this->routes[$route->getName()] = $route;
        }
    }

    /**
     * @return array<string, RouteInterface>
     */
    public function getRoutesByName(): array
    {
        return $this->routes;
    }
}
