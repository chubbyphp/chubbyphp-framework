<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouteMatcherMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly RouteMatcherInterface $routeMatcher) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->routeMatcher->match($request);
        $request = $request->withAttribute('route', $route);

        foreach ($route->getAttributes() as $attribute => $value) {
            $request = $request->withAttribute($attribute, $value);
        }

        return $handler->handle($request);
    }
}
