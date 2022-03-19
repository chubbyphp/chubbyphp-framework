<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Chubbyphp\Framework\Collection;
use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Route implements RouteInterface
{
    /**
     * @var Collection<MiddlewareInterface>
     */
    private Collection $middlewares;

    /**
     * @var array<string, string>
     */
    private array $attributes = [];

    /**
     * @param array<MiddlewareInterface> $middlewares
     * @param array<string, mixed>       $pathOptions
     */
    private function __construct(
        private string $method,
        private string $path,
        private string $name,
        private RequestHandlerInterface $requestHandler,
        array $middlewares = [],
        private array $pathOptions = []
    ) {
        $this->middlewares = new Collection($middlewares, [MiddlewareInterface::class]);
    }

    /**
     * @param array<MiddlewareInterface> $middlewares
     * @param array<string, mixed>       $pathOptions
     */
    public static function create(
        string $method,
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = [],
        array $pathOptions = []
    ): self {
        return new self($method, $path, $name, $requestHandler, $middlewares, $pathOptions);
    }

    /**
     * @param array<MiddlewareInterface> $middlewares
     * @param array<string, mixed>       $pathOptions
     */
    public static function delete(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = [],
        array $pathOptions = []
    ): self {
        return new self(RequestMethod::METHOD_DELETE, $path, $name, $requestHandler, $middlewares, $pathOptions);
    }

    /**
     * @param array<MiddlewareInterface> $middlewares
     * @param array<string, mixed>       $pathOptions
     */
    public static function get(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = [],
        array $pathOptions = []
    ): self {
        return new self(RequestMethod::METHOD_GET, $path, $name, $requestHandler, $middlewares, $pathOptions);
    }

    /**
     * @param array<MiddlewareInterface> $middlewares
     * @param array<string, mixed>       $pathOptions
     */
    public static function head(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = [],
        array $pathOptions = []
    ): self {
        return new self(RequestMethod::METHOD_HEAD, $path, $name, $requestHandler, $middlewares, $pathOptions);
    }

    /**
     * @param array<MiddlewareInterface> $middlewares
     * @param array<string, mixed>       $pathOptions
     */
    public static function options(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = [],
        array $pathOptions = []
    ): self {
        return new self(RequestMethod::METHOD_OPTIONS, $path, $name, $requestHandler, $middlewares, $pathOptions);
    }

    /**
     * @param array<MiddlewareInterface> $middlewares
     * @param array<string, mixed>       $pathOptions
     */
    public static function patch(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = [],
        array $pathOptions = []
    ): self {
        return new self(RequestMethod::METHOD_PATCH, $path, $name, $requestHandler, $middlewares, $pathOptions);
    }

    /**
     * @param array<MiddlewareInterface> $middlewares
     * @param array<string, mixed>       $pathOptions
     */
    public static function post(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = [],
        array $pathOptions = []
    ): self {
        return new self(RequestMethod::METHOD_POST, $path, $name, $requestHandler, $middlewares, $pathOptions);
    }

    /**
     * @param array<MiddlewareInterface> $middlewares
     * @param array<string, mixed>       $pathOptions
     */
    public static function put(
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = [],
        array $pathOptions = []
    ): self {
        return new self(RequestMethod::METHOD_PUT, $path, $name, $requestHandler, $middlewares, $pathOptions);
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
        return $this->middlewares->toArray();
    }

    public function getRequestHandler(): RequestHandlerInterface
    {
        return $this->requestHandler;
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
