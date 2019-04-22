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
     * @param string $name
     *
     * @return self
     */
    public static function createForMissingRoute(string $name): self
    {
        return new self(sprintf('Missing route: "%s"', $name), 1);
    }
}
