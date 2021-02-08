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
     * @var array<string, mixed>
     */
    private $pathOptions = [];

    /**
     * @var array<MiddlewareInterface>
     */
    private $middlewares = [];

    /**
     * @var array<GroupInterface>|array<RouteInterface>
     */
    private $children = [];

    private function __construct(string $path)
    {
        $this->path = $path;
    }

    public static function create(string $path): self
    {
        return new self($path);
    }

    /**
     * @param array<string, mixed> $pathOptions
     */
    public function pathOptions(array $pathOptions): self
    {
        $this->pathOptions = $pathOptions;

        return $this;
    }

    /**
     * @param array<MiddlewareInterface> $middlewares
     */
    public function middlewares(array $middlewares): self
    {
        foreach ($middlewares as $middleware) {
            $this->middleware($middleware);
        }

        return $this;
    }

    public function middleware(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    public function group(Group $group): self
    {
        $this->children[] = $group;

        return $this;
    }

    public function route(RouteInterface $route): self
    {
        $this->children[] = $route;

        return $this;
    }

    /**
     * @return array<RouteInterface>
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

    private function createRoute(RouteInterface $route): RouteInterface
    {
        return Route::create(
            $route->getMethod(),
            $this->path.$route->getPath(),
            $route->getName(),
            $route->getRequestHandler()
        )
            ->pathOptions(array_merge_recursive($this->pathOptions, $route->getPathOptions()))
            ->middlewares(array_merge($this->middlewares, $route->getMiddlewares()))
        ;
    }
}
