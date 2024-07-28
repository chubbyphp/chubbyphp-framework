<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Exceptions;

final class RouteGenerationException extends RouterException
{
    private function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param array<string, string> $attributes
     */
    public static function create(string $name, string $path, array $attributes, ?\Throwable $previous = null): self
    {
        return new self(
            \sprintf(
                'Route generation for route "%s" with path "%s" with attributes "%s" failed.%s',
                $name,
                $path,
                json_encode([] !== $attributes ? $attributes : new \stdClass(), JSON_THROW_ON_ERROR),
                null !== $previous ? ' '.$previous->getMessage() : '',
            ),
            3,
            $previous
        );
    }
}
