<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

interface RoutesInterface
{
    /**
     * @return array<string, RouteInterface>
     */
    public function getRoutesByName(): array;
}
