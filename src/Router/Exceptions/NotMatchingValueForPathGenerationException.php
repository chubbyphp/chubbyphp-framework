<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Exceptions;

use Chubbyphp\Framework\Router\RouterException;

/**
 * @deprecated use RouteGenerationException
 */
final class NotMatchingValueForPathGenerationException extends RouterException
{
    private string $name;

    private string $attribute;

    private string $value;

    private string $pattern;

    private function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        @trigger_error(
            sprintf('Use "%s" instead of "%s"', RouteGenerationException::class, self::class),
            E_USER_DEPRECATED
        );

        parent::__construct($message, $code, $previous);
    }

    public static function create(string $name, string $attribute, string $value, string $pattern): self
    {
        $self = new self(
            sprintf(
                'Not matching value "%s" with pattern "%s" on attribute "%s" while path generation for route: "%s"',
                $value,
                $pattern,
                $attribute,
                $name
            ),
            4
        );

        $self->name = $name;
        $self->attribute = $attribute;
        $self->value = $value;
        $self->pattern = $pattern;

        return $self;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }
}
