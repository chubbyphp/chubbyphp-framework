<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\RequestHandler;

use Chubbyphp\Framework\Middleware\PipeMiddleware;
use Chubbyphp\Framework\Router\Exceptions\MissingRouteAttributeOnRequestException;
use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouteRequestHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('route');

        if (!$route instanceof RouteInterface) {
            throw MissingRouteAttributeOnRequestException::create($route);
        }

        return (new PipeMiddleware($route->getMiddlewares()))->process($request, $route->getRequestHandler());
    }
}
