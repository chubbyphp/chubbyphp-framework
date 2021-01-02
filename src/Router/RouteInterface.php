<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface RouteInterface
{
    public function getName(): string;

    public function getMethod(): string;

    public function getPath(): string;

    /**
     * @return array<string, mixed>
     */
    public function getPathOptions(): array;

    /**
     * @return array<MiddlewareInterface>
     */
    public function getMiddlewares(): array;

    public function getRequestHandler(): RequestHandlerInterface;

    /**
     * @param array<string, string> $attributes
     */
    public function withAttributes(array $attributes): RouteInterface;

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array;
}
