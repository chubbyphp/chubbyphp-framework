<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Exceptions;

final class MissingRouteByNameException extends RouterException
{
    private function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(string $name): self
    {
        return new self(sprintf('Missing route: "%s"', $name), 2);
    }
}
