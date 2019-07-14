<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
    private $handler;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @param string                  $method
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $handler
     */
    private function __construct(string $method, string $path, string $name, RequestHandlerInterface $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->name = $name;
        $this->handler = $handler;
    }

    /**
     * @param string                  $method
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $handler
     *
     * @return self
     */
    public static function create(
        string $method,
        string $path,
        string $name,
        RequestHandlerInterface $handler
    ): self {
        return new self($method, $path, $name, $handler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $handler
     *
     * @return self
     */
    public static function delete(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::DELETE, $path, $name, $handler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $handler
     *
     * @return self
     */
    public static function get(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::GET, $path, $name, $handler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $handler
     *
     * @return self
     */
    public static function head(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::HEAD, $path, $name, $handler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $handler
     *
     * @return self
     */
    public static function options(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::OPTIONS, $path, $name, $handler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $handler
     *
     * @return self
     */
    public static function patch(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::PATCH, $path, $name, $handler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $handler
     *
     * @return self
     */
    public static function post(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::POST, $path, $name, $handler);
    }

    /**
     * @param string                  $path
     * @param string                  $name
     * @param RequestHandlerInterface $handler
     *
     * @return self
     */
    public static function put(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::PUT, $path, $name, $handler);
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
        return $this->handler;
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
}
