<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Exceptions;

use Chubbyphp\Framework\Router\RouterException;

final class MissingRouteByNameException extends RouterException
{
    private string $name;

    private function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(string $name): self
    {
        $self = new self(sprintf('Missing route: "%s"', $name), 1);
        $self->name = $name;

        return $self;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
