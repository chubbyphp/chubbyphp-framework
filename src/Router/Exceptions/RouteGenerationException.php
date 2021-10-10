<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Exceptions;

final class RouteGenerationException extends RouterException
{
    private string $name;

    private string $path;

    /** @var array<string, string> */
    private array $attributes;

    private function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param array<string, string> $attributes
     */
    public static function create(string $name, string $path, array $attributes, ?\Throwable $previous = null): self
    {
        $self = new self(sprintf(
            'Route generation for route "%s" with path "%s" with attributes "%s" failed.%s',
            $name,
            $path,
            json_encode([] !== $attributes ? $attributes : new \stdClass()),
            null !== $previous ? ' '.$previous->getMessage() : '',
        ), 3, $previous);
        $self->name = $name;
        $self->path = $path;
        $self->attributes = $attributes;

        return $self;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
