<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouteDispatcherInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return RouteInterface
     */
    public function dispatch(ServerRequestInterface $request): RouteInterface;
}
