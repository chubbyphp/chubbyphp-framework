<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class Group implements GroupInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $pathOptions = [];

    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares = [];

    /**
     * @var Route[]
     */
    private $routes = [];

    /**
     * @var Group[]
     */
    private $groups = [];

    /**
     * @param string                  $method
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     */
    private function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     *
     * @return self
     */
    public static function create(string $path): self
    {
        return new self($path);
    }

    /**
     * @param array $pathOptions
     *
     * @return self
     */
    public function pathOptions(array $pathOptions): self
    {
        $this->pathOptions = $pathOptions;

        return $this;
    }

    /**
     * @param MiddlewareInterface $middleware
     *
     * @return self
     */
    public function middleware(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    /**
     * @param Route $route
     *
     * @return self
     */
    public function route(Route $route): self
    {
        $this->routes[] = $route;

        return $this;
    }

    /**
     * @param Group $group
     *
     * @return self
     */
    public function group(Group $group): self
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * @return RouteInterface[]
     */
    public function getRoutes(): array
    {
        $routes = [];
        foreach ($this->routes as $route) {
            $routes[$route->getName()] = $this->createRoute($route);
        }

        foreach ($this->groups as $group) {
            foreach ($group->getRoutes() as $route) {
                $routes[$route->getName()] = $this->createRoute($route);
            }
        }

        return $routes;
    }

    /**
     * @param RouteInterface $route
     *
     * @return RouteInterface
     */
    private function createRoute(RouteInterface $route): RouteInterface
    {
        return Route::create(
            $route->getMethod(),
            $this->path.$route->getPath(),
            $route->getName(),
            $route->getRequestHandler()
        )
        ->pathOptions(array_merge_recursive($this->pathOptions, $route->getPathOptions()))
        ->middlewares(array_merge($this->middlewares, $route->getMiddlewares()));
    }
}
