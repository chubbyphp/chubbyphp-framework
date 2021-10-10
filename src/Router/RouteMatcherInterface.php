<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Chubbyphp\Framework\Router\Exceptions\RouterException;
use Psr\Http\Message\ServerRequestInterface;

interface RouteMatcherInterface
{
    /**
     * @throws RouterException
     */
    public function match(ServerRequestInterface $request): RouteInterface;
}
