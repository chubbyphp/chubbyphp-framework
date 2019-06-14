<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

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
     * @var GroupInterface[]|RouteInterface[]
     */
    private $children = [];

    /**
     * @param string $path
     */
    private function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param string $path
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
     * @param MiddlewareInterface[] $middlewares
     *
     * @return self
     */
    public function middlewares(array $middlewares): self
    {
        foreach ($middlewares as $middleware) {
            $this->middleware($middleware);
        }

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
     * @param Group $group
     *
     * @return self
     */
    public function group(Group $group): self
    {
        $this->children[] = $group;

        return $this;
    }

    /**
     * @param RouteInterface $route
     *
     * @return self
     */
    public function route(RouteInterface $route): self
    {
        $this->children[] = $route;

        return $this;
    }

    /**
     * @return RouteInterface[]
     */
    public function getRoutes(): array
    {
        $routes = [];
        foreach ($this->children as $child) {
            if ($child instanceof GroupInterface) {
                foreach ($child->getRoutes() as $route) {
                    $routes[] = $this->createRoute($route);
                }
            } else {
                $routes[] = $this->createRoute($child);
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
