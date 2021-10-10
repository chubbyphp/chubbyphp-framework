<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Exceptions;

use Chubbyphp\Framework\Router\RouterException;

final class RouteGenerationException extends RouterException
{
    private string $name;

    private string $pattern;

    /** @var array<string, string> */
    private array $attributes;

    private function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param array<string, string> $attributes
     */
    public static function create(string $name, string $pattern, array $attributes, ?\Throwable $previous = null): self
    {
        $self = new self(sprintf(
            'Route generation for route "%s" with pattern "%s" with attributes "%s" failed.',
            $name,
            $pattern,
            json_encode($attributes)
        ), 3, $previous);
        $self->name = $name;
        $self->pattern = $pattern;
        $self->attributes = $attributes;

        return $self;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
