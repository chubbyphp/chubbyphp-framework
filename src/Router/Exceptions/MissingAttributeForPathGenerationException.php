<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Exceptions;

use Chubbyphp\Framework\Router\RouterException;

/**
 * @deprecated use RouteGenerationException
 */
final class MissingAttributeForPathGenerationException extends RouterException
{
    private string $name;

    private string $attribute;

    private function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        @trigger_error(
            sprintf('Use "%s" instead of "%s"', RouteGenerationException::class, self::class),
            E_USER_DEPRECATED
        );

        parent::__construct($message, $code, $previous);
    }

    public static function create(string $name, string $attribute): self
    {
        $self = new self(sprintf('Missing attribute "%s" while path generation for route: "%s"', $attribute, $name), 3);
        $self->name = $name;
        $self->attribute = $attribute;

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
}
