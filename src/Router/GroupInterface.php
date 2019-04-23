<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

interface GroupInterface
{
    /**
     * @return RouteInterface[]
     */
    public function getRoutes(): array;
}
