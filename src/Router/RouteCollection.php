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
    private $patternStack = [];

    /**
     * @var array[]
     */
    private $middlewaresStack = [];

    /**
     * @param string $pattern
     * @param array  $middlewares
     *
     * @return self
     */
    public function group(string $pattern, array $middlewares = []): self
    {
        $this->patternStack[] = $pattern;
        $this->middlewaresStack[] = $middlewares;

        return $this;
    }

    /**
     * @return self
     */
    public function end(): self
    {
        array_pop($this->patternStack);
        array_pop($this->middlewaresStack);

        return $this;
    }

    /**
     * @param string                  $pattern
     * @param string                  $method
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     * @param array                   $middlewares
     *
     * @return self
     */
    public function route(
        string $pattern,
        string $method,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = []
    ): self {
        $this->routes[$name] = new Route(
            $this->getPattern($pattern),
            $method,
            $name,
            $requestHandler,
            $this->getMiddlewares($middlewares)
        );

        return $this;
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    private function getPattern(string $pattern): string
    {
        return implode('', $this->patternStack).$pattern;
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
