<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\RequestHandler;

use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\Router\Exceptions\MissingRouteAttributeOnRequestException;
use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouteRequestHandler implements RequestHandlerInterface
{
    private MiddlewareDispatcherInterface $middlewareDispatcher;

    public function __construct(MiddlewareDispatcherInterface $middlewareDispatcher)
    {
        $this->middlewareDispatcher = $middlewareDispatcher;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('route');

        if (!$route instanceof RouteInterface) {
            throw MissingRouteAttributeOnRequestException::create($route);
        }

        return $this->middlewareDispatcher->dispatch(
            $route->getMiddlewares(),
            $route->getRequestHandler(),
            $request
        );
    }
}
