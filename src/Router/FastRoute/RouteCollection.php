<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\FastRoute;

use Chubbyphp\Framework\Router\RouteCollectionException;
use Chubbyphp\Framework\Router\RouteCollectionInterface;
use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Chubbyphp\Framework\Router\Route;

final class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var RouteInterface[]
     */
    private $routes = [];

    /**
     * @var string[]
     */
    private $pathStack = [];

    /**
     * @var array[]
     */
    private $middlewaresStack = [];

    /**
     * @var bool
     */
    private $freeze = false;

    /**
     * @param string $path
     * @param array  $middlewares
     *
     * @return self
     *
     * @throws RouteCollectionException
     */
    public function group(string $path, array $middlewares = []): self
    {
        if ($this->freeze) {
            throw RouteCollectionException::createFreezeException();
        }

        $this->validateMiddlewares(__METHOD__, $middlewares);

        $this->pathStack[] = $path;
        $this->middlewaresStack[] = $middlewares;

        return $this;
    }

    /**
     * @return self
     *
     * @throws RouteCollectionException
     */
    public function end(): self
    {
        if ($this->freeze) {
            throw RouteCollectionException::createFreezeException();
        }

        array_pop($this->pathStack);
        array_pop($this->middlewaresStack);

        return $this;
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     * @param array                   $middlewares
     *
     * @return self
     *
     * @throws RouteCollectionException
     */
    public function delete(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = []
    ): self {
        return $this->route($path, RouteInterface::DELETE, $name, $requestHandler, $middlewares);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     * @param array                   $middlewares
     *
     * @return self
     *
     * @throws RouteCollectionException
     */
    public function get(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = []
    ): self {
        return $this->route($path, RouteInterface::GET, $name, $requestHandler, $middlewares);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     * @param array                   $middlewares
     *
     * @return self
     *
     * @throws RouteCollectionException
     */
    public function head(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = []
    ): self {
        return $this->route($path, RouteInterface::HEAD, $name, $requestHandler, $middlewares);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     * @param array                   $middlewares
     *
     * @return self
     *
     * @throws RouteCollectionException
     */
    public function options(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = []
    ): self {
        return $this->route($path, RouteInterface::OPTIONS, $name, $requestHandler, $middlewares);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     * @param array                   $middlewares
     *
     * @return self
     *
     * @throws RouteCollectionException
     */
    public function patch(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = []
    ): self {
        return $this->route($path, RouteInterface::PATCH, $name, $requestHandler, $middlewares);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     * @param array                   $middlewares
     *
     * @return self
     *
     * @throws RouteCollectionException
     */
    public function post(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = []
    ): self {
        return $this->route($path, RouteInterface::POST, $name, $requestHandler, $middlewares);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     * @param array                   $middlewares
     *
     * @return self
     *
     * @throws RouteCollectionException
     */
    public function put(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = []
    ): self {
        return $this->route($path, RouteInterface::PUT, $name, $requestHandler, $middlewares);
    }

    /**
     * @param string                  $path
     * @param string                  $method
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     * @param array                   $middlewares
     *
     * @return self
     *
     * @throws RouteCollectionException
     */
    private function route(
        string $path,
        string $method,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = []
    ): self {
        if ($this->freeze) {
            throw RouteCollectionException::createFreezeException();
        }

        $this->validateMiddlewares(__METHOD__, $middlewares);

        $this->routes[$name] = new Route(
            $this->getPath($path),
            [],
            $method,
            $name,
            $requestHandler,
            $this->getMiddlewares($middlewares)
        );

        return $this;
    }

    /**
     * @param string                $method
     * @param MiddlewareInterface[] $middlewares
     */
    private function validateMiddlewares(string $method, array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof MiddlewareInterface) {
                throw new \TypeError(
                    sprintf(
                        '%s expects parameter 1 to be %s[], %s[] given',
                        $method,
                        MiddlewareInterface::class,
                        is_object($middleware) ? get_class($middleware) : gettype($middleware)
                    )
                );
            }
        }
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getPath(string $path): string
    {
        return implode('', $this->pathStack).$path;
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
        $this->freeze = true;

        return $this->routes;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $string = '';
        foreach ($this->getRoutes() as $route) {
            $string .= $route.PHP_EOL;
        }

        return trim($string);
    }
}
