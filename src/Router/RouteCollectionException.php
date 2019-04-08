<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

final class RouteCollectionException extends \RuntimeException
{
    /**
     * @param mixed ...$args
     */
    private function __construct(...$args)
    {
        parent::__construct(...$args);
    }

    /**
     * @param string $path
     *
     * @return self
     */
    public static function createFreezeException(): self
    {
        return new self('The route collection is frozen', 1);
    }
}
