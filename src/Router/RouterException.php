<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Chubbyphp\Framework\Router\Exceptions\MethodNotAllowedException;
use Chubbyphp\Framework\Router\Exceptions\MissingAttributeForPathGenerationException;
use Chubbyphp\Framework\Router\Exceptions\MissingRouteAttributeOnRequestException;
use Chubbyphp\Framework\Router\Exceptions\MissingRouteByNameException;
use Chubbyphp\Framework\Router\Exceptions\NotFoundException;
use Chubbyphp\Framework\Router\Exceptions\NotMatchingValueForPathGenerationException;

/**
 * @deprecated 3.0
 */
class RouterException extends \RuntimeException
{
    protected function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function createForNotFound(string $path): self
    {
        return NotFoundException::create($path);
    }

    /**
     * @param array<string> $methods
     */
    public static function createForMethodNotAllowed(string $method, array $methods, string $path): self
    {
        return MethodNotAllowedException::create($path, $method, $methods);
    }

    public static function createForMissingRoute(string $name): self
    {
        return MissingRouteByNameException::create($name);
    }

    /**
     * @param mixed $route
     */
    public static function createForMissingRouteAttribute($route): self
    {
        return MissingRouteAttributeOnRequestException::create($route);
    }

    public static function createForPathGenerationMissingAttribute(string $name, string $attribute): self
    {
        return MissingAttributeForPathGenerationException::create($name, $attribute);
    }

    public static function createForPathGenerationNotMatchingAttribute(
        string $name,
        string $attribute,
        string $value,
        string $pattern
    ): self {
        return NotMatchingValueForPathGenerationException::create($name, $attribute, $value, $pattern);
    }
}
