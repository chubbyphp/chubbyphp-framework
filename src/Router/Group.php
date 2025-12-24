<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Server\MiddlewareInterface;

final class Group implements GroupInterface
{
    /**
     * @param array<GroupInterface|RouteInterface> $children
     * @param array<MiddlewareInterface>           $middlewares
     * @param array<string, mixed>                 $pathOptions
     */
    private function __construct(
        private readonly string $path,
        private readonly array $children = [],
        private readonly array $middlewares = [],
        private readonly array $pathOptions = []
    ) {}

    /**
     * @param array<GroupInterface|RouteInterface> $children
     * @param array<MiddlewareInterface>           $middlewares
     * @param array<string, mixed>                 $pathOptions
     */
    public static function create(
        string $path,
        array $children = [],
        array $middlewares = [],
        array $pathOptions = []
    ): self {
        return new self($path, $children, $middlewares, $pathOptions);
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
            $route->getRequestHandler(),
            array_merge($this->middlewares, $route->getMiddlewares()),
            array_merge_recursive($this->pathOptions, $route->getPathOptions())
        );
    }
}
