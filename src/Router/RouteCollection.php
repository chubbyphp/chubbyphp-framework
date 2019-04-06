<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Server\RequestHandlerInterface;

final class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var RouteInterface[]
     */
    private $routes;

    /**
     * @var string[]
     */
    private $pathStack = [];

    /**
     * @var array[]
     */
    private $middlewaresStack = [];

    /**
     * @param string $path
     * @param array  $middlewares
     *
     * @return self
     */
    public function group(string $path, array $middlewares = []): self
    {
        $this->pathStack[] = $path;
        $this->middlewaresStack[] = $middlewares;

        return $this;
    }

    /**
     * @return self
     */
    public function end(): self
    {
        array_pop($this->pathStack);
        array_pop($this->middlewaresStack);

        return $this;
    }

    /**
     * @param string                  $path
     * @param string                  $method
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     * @param array                   $middlewares
     *
     * @return self
     */
    public function route(
        string $path,
        string $method,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = []
    ): self {
        $this->routes[$name] = new Route(
            $this->getPath($path),
            $method,
            $name,
            $requestHandler,
            $this->getMiddlewares($middlewares)
        );

        return $this;
    }

    /**
     * @param string $routePath
     *
     * @return string
     */
    private function getPath(string $routePath): string
    {
        return implode('', $this->pathStack).$routePath;
    }

    /**
     * @param MiddlewareInterface[] $routeMiddlewares
     *
     * @return MiddlewareInterface[]
     */
    private function getMiddlewares(array $routeMiddlewares): array
    {
        $middlewares = [];
        foreach ($this->middlewaresStack as $middlewaresFromStack) {
            $middlewares = array_merge($middlewares, $middlewaresFromStack);
        }

        return array_merge($middlewares, $routeMiddlewares);
    }

    /**
     * @return RouteInterface[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
