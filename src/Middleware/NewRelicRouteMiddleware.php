<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class NewRelicRouteMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (extension_loaded('newrelic')) {
            /** @var RouteInterface $route */
            $route = $request->getAttribute('route');

            newrelic_name_transaction($route->getName());
        }

        return $handler->handle($request);
    }
}
