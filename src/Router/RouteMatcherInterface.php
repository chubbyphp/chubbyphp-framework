<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouteMatcherInterface
{
    public function match(ServerRequestInterface $request): RouteInterface;
}
