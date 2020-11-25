<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface RouteInterface
{
    /** @deprecated */
    public const DELETE = RequestMethod::METHOD_DELETE;

    /** @deprecated */
    public const GET = RequestMethod::METHOD_GET;

    /** @deprecated */
    public const HEAD = RequestMethod::METHOD_HEAD;

    /** @deprecated */
    public const OPTIONS = RequestMethod::METHOD_OPTIONS;

    /** @deprecated */
    public const PATCH = RequestMethod::METHOD_PATCH;

    /** @deprecated */
    public const POST = RequestMethod::METHOD_POST;

    /** @deprecated */
    public const PUT = RequestMethod::METHOD_PUT;

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
