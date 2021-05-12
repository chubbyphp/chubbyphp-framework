<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Server\MiddlewareInterface;

final class Group implements GroupInterface
{
    private string $path;

    /**
     * @var array<MiddlewareInterface>
     */
    private array $middlewares = [];

    /**
     * @var array<GroupInterface|RouteInterface>
     */
    private array $children = [];

    /**
     * @var array<string, mixed>
     */
    private array $pathOptions = [];

    /**
     * @param array<GroupInterface|RouteInterface> $children
     * @param array<MiddlewareInterface>           $middlewares
     * @param array<string, mixed>                 $pathOptions
     */
    private function __construct(string $path, array $children = [], array $middlewares = [], array $pathOptions = [])
    {
        $this->path = $path;
        $this->pathOptions = $pathOptions;

        foreach ($children as $child) {
            $this->addChild($child);
        }

        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
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

    /**
     * @deprecated
     *
     * @param array<string, mixed> $pathOptions
     */
    public function pathOptions(array $pathOptions): self
    {
        @trigger_error(
            sprintf('Use "$pathOptions" parameter instead of "%s()"', __METHOD__),
            E_USER_DEPRECATED
        );

        $this->pathOptions = $pathOptions;

        return $this;
    }

    /**
     * @deprecated
     *
     * @param array<MiddlewareInterface> $middlewares
     */
    public function middlewares(array $middlewares): self
    {
        @trigger_error(
            sprintf('Use "$middlewares" parameter instead of "%s()"', __METHOD__),
            E_USER_DEPRECATED
        );

        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }

        return $this;
    }

    /**
     * @deprecated
     */
    public function middleware(MiddlewareInterface $middleware): self
    {
        @trigger_error(
            sprintf('Use "$middlewares" parameter instead of "%s()"', __METHOD__),
            E_USER_DEPRECATED
        );

        $this->middlewares[] = $middleware;

        return $this;
    }

    /**
     * @deprecated
     */
    public function group(Group $group): self
    {
        @trigger_error(
            sprintf('Use "$children" parameter instead of "%s()"', __METHOD__),
            E_USER_DEPRECATED
        );

        $this->children[] = $group;

        return $this;
    }

    /**
     * @deprecated
     */
    public function route(RouteInterface $route): self
    {
        @trigger_error(
            sprintf('Use "$children" parameter instead of "%s()"', __METHOD__),
            E_USER_DEPRECATED
        );

        $this->children[] = $route;

        return $this;
    }

    /**
     * @param GroupInterface|RouteInterface|mixed $child
     */
    private function addChild($child): void
    {
        if ($child instanceof GroupInterface || $child instanceof RouteInterface) {
            $this->children[] = $child;

            return;
        }

        throw new \TypeError(
            sprintf(
                '%s::addChild() expects parameter 1 to be %s|%s, %s given',
                self::class,
                GroupInterface::class,
                RouteInterface::class,
                is_object($child) ? get_class($child) : gettype($child)
            )
        );
    }

    private function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
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
