<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Chubbyphp\Framework\Collection;
use Psr\Http\Server\MiddlewareInterface;

final class Group implements GroupInterface
{
    /**
     * @var array<MiddlewareInterface>
     */
    private array $middlewares;

    /**
     * @var array<GroupInterface|RouteInterface>
     */
    private array $children;

    /**
     * @param array<GroupInterface|RouteInterface> $children
     * @param array<MiddlewareInterface>           $middlewares
     * @param array<string, mixed>                 $pathOptions
     */
    private function __construct(
        private string $path,
        array $children = [],
        array $middlewares = [],
        private array $pathOptions = []
    ) {
        $this->children = (new Collection($children, [GroupInterface::class, RouteInterface::class]))->toArray();
        $this->middlewares = (new Collection($middlewares, [MiddlewareInterface::class]))->toArray();
    }

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
