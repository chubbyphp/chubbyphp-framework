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
    private $path;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $name;

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares = [];

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @param string                  $path
     * @param string                  $method
     * @param string                  $name
     * @param RequestHandlerInterface $requestHandler
     * @param MiddlewareInterface[]   $middlewares
     */
    public function __construct(
        string $path,
        string $method,
        string $name,
        RequestHandlerInterface $requestHandler,
        array $middlewares = []
    ) {
        $this->path = $path;
        $this->method = $method;
        $this->name = $name;
        $this->requestHandler = $requestHandler;

        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
    }

    /**
     * @param MiddlewareInterface $middleware
     */
    private function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return RequestHandlerInterface
     */
    public function getRequestHandler(): RequestHandlerInterface
    {
        return $this->requestHandler;
    }

    /**
     * @return MiddlewareInterface[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @param array $attributes
     *
     * @return RouteInterface
     */
    public function withAttributes(array $attributes): RouteInterface
    {
        $self = clone $this;
        $self->attributes = $attributes;

        return $self;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
