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
     * @var array<string, mixed>
     */
    private $pathOptions = [];

    /**
     * @var array<MiddlewareInterface>
     */
    private $middlewares = [];

    /**
     * @var RequestHandlerInterface
     */
    private $handler;

    /**
     * @var array<string, string>
     */
    private $attributes = [];

    private function __construct(string $method, string $path, string $name, RequestHandlerInterface $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->name = $name;
        $this->handler = $handler;
    }

    public static function create(
        string $method,
        string $path,
        string $name,
        RequestHandlerInterface $handler
    ): self {
        return new self($method, $path, $name, $handler);
    }

    public static function delete(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::DELETE, $path, $name, $handler);
    }

    public static function get(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::GET, $path, $name, $handler);
    }

    public static function head(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::HEAD, $path, $name, $handler);
    }

    public static function options(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::OPTIONS, $path, $name, $handler);
    }

    public static function patch(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::PATCH, $path, $name, $handler);
    }

    public static function post(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::POST, $path, $name, $handler);
    }

    public static function put(string $path, string $name, RequestHandlerInterface $handler): self
    {
        return new self(RouteInterface::PUT, $path, $name, $handler);
    }

    /**
     * @param array<string, string> $pathOptions
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPathOptions(): array
    {
        return $this->pathOptions;
    }

    /**
     * @return array<MiddlewareInterface>
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function getRequestHandler(): RequestHandlerInterface
    {
        return $this->handler;
    }

    /**
     * @param array<string, string> $attributes
     */
    public function withAttributes(array $attributes): RouteInterface
    {
        $clone = clone $this;
        $clone->attributes = $attributes;

        return $clone;
    }

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
