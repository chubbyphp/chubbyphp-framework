<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

interface RouteCollectionInterface
{
    /**
     * Routes with name as key.
     *
     * @return RouteInterface[]
     */
    public function getRoutes(): array;

    /**
     * @return string
     */
    public function __toString(): string;
}
