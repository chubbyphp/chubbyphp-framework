<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Chubbyphp\Framework\Debug;
use Chubbyphp\Framework\DebugInterface;
use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Route implements DebugInterface, RouteInterface
{
    private string $name;

    private string $method;

    private string $path;

    /**
     * @var array<string, mixed>
     */
    private array $pathOptions = [];

    /**
     * @var array<MiddlewareInterface>
     */
    private array $middlewares = [];

    private RequestHandlerInterface $requestHandler;

    /**
     * @var array<string, string>
     */
    private array $attributes = [];

    /**
     * @param array<MiddlewareInterface> $middlewares
     * @param array<string, mixed>       $pathOptions
     */
    private function __construct(
        string $method,
        string $path,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = [],
        array $pathOptions = []
    ) {
        $this->method = $method;
        $this->path = $path;
        $this->name = $name;
        $this->requestHandler = $requestHandler;
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
        $this->pathOptions = $pathOptions;
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
        return $this->middlewares;
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

    /**
     * @deprecated
     *
     * @param array<string, mixed> $pathOptions
     */
    public function pathOptions(array $pathOptions): self
    {
        @trigger_error(
            sprintf('Use "$pathOptions" parameter instead of instead of "%s()"', __METHOD__),
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
            sprintf('Use "$middlewares" parameter instead of instead of "%s()"', __METHOD__),
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
            sprintf('Use "$middlewares" parameter instead of instead of "%s()"', __METHOD__),
            E_USER_DEPRECATED
        );

        $this->middlewares[] = $middleware;

        return $this;
    }

    public function debug(): array
    {
        return Debug::debug([
            'name' => $this->name,
            'method' => $this->method,
            'path' => $this->path,
            'pathOptions' => $this->pathOptions,
            'middlewares' => $this->middlewares,
            'requestHandler' => $this->requestHandler,
        ]);
    }

    private function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }
}
