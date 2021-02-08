<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface RouteInterface
{
    public const DELETE = 'DELETE';
    public const GET = 'GET';
    public const HEAD = 'HEAD';
    public const OPTIONS = 'OPTIONS';
    public const PATCH = 'PATCH';
    public const POST = 'POST';
    public const PUT = 'PUT';

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
