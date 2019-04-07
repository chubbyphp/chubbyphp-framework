<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

final class UrlGeneratorException extends \RuntimeException
{
    /**
     * @param mixed ...$args
     */
    private function __construct(...$args)
    {
        parent::__construct(...$args);
    }

    /**
     * @return self
     */
    public static function createForMissingParameter(): self
    {
        return new self('Missing parameters');
    }

    /**
     * @param string $parameter
     * @param string $value
     * @param string $pattern
     *
     * @return self
     */
    public static function createForInvalidParameter(string $parameter, string $value, string $pattern): self
    {
        return new self(
            sprintf(
                'Parameter "%s" with value "%s" does not match "%s"',
                $parameter,
                $value,
                $pattern
            )
        );
    }
}
