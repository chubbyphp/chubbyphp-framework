<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Exceptions;

use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\Router\RouterException;

final class MissingRouteAttributeOnRequestException extends RouterException
{
    private function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param mixed $route
     */
    public static function create($route): self
    {
        return new self(sprintf(
            'Request attribute "route" missing or wrong type "%s", please add the "%s" middleware',
            is_object($route) ? get_class($route) : gettype($route),
            RouteMatcherMiddleware::class
        ), 2);
    }
}
