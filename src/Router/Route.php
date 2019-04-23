<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class Route implements RouteInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $method;

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
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @param string                  $method
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     */
    private function __construct(string $method, string $path, string $name, RequestHandlerInterface $requestHandler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->name = $name;
        $this->requestHandler = $requestHandler;
    }

    /**
     * @param string                  $method
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     *
     * @return self
     */
    public static function create(
        string $method,
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler
    ): self {
        return new self($method, $path, $name, $requestHandler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     *
     * @return self
     */
    public static function delete(string $path, string $name, RequestHandlerInterface $requestHandler): self
    {
        return new self(RouteInterface::DELETE, $path, $name, $requestHandler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     *
     * @return self
     */
    public static function get(string $path, string $name, RequestHandlerInterface $requestHandler): self
    {
        return new self(RouteInterface::GET, $path, $name, $requestHandler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     *
     * @return self
     */
    public static function head(string $path, string $name, RequestHandlerInterface $requestHandler): self
    {
        return new self(RouteInterface::HEAD, $path, $name, $requestHandler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     *
     * @return self
     */
    public static function options(string $path, string $name, RequestHandlerInterface $requestHandler): self
    {
        return new self(RouteInterface::OPTIONS, $path, $name, $requestHandler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     *
     * @return self
     */
    public static function patch(string $path, string $name, RequestHandlerInterface $requestHandler): self
    {
        return new self(RouteInterface::PATCH, $path, $name, $requestHandler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     *
     * @return self
     */
    public static function post(string $path, string $name, RequestHandlerInterface $requestHandler): self
    {
        return new self(RouteInterface::POST, $path, $name, $requestHandler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     *
     * @return self
     */
    public static function put(string $path, string $name, RequestHandlerInterface $requestHandler): self
    {
        return new self(RouteInterface::PUT, $path, $name, $requestHandler);
    }

    /**
     * @param array $config
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getPathOptions(): array
    {
        return $this->pathOptions;
    }

    /**
     * @return MiddlewareInterface[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @return RequestHandlerInterface
     */
    public function getRequestHandler(): RequestHandlerInterface
    {
        return $this->requestHandler;
    }

    /**
     * @param array $attributes
     *
     * @return RouteInterface
     */
    public function withAttributes(array $attributes): RouteInterface
    {
        $clone = clone $this;
        $clone->attributes = $attributes;

        return $clone;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name.'::'.$this->method.'::'.$this->path.'::'.json_encode($this->pathOptions);
    }
}
