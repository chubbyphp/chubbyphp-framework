<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouteMatcherInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return RouteInterface
     */
    public function match(ServerRequestInterface $request): RouteInterface;
}
