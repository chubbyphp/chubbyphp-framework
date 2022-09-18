<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

interface RoutesByNameInterface
{
    /**
     * @return array<string, RouteInterface>
     */
    public function getRoutesByName(): array;
}
