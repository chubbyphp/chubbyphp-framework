<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

interface GroupInterface
{
    /**
     * @return array<RouteInterface>
     */
    public function getRoutes(): array;
}
