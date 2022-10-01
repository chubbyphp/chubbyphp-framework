<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MiddlewareDispatcher implements MiddlewareDispatcherInterface
{
    /**
     * @param array<MiddlewareInterface> $middlewares
     */
    public function dispatch(
        array $middlewares,
        RequestHandlerInterface $handler,
        ServerRequestInterface $request
    ): ResponseInterface {
        return array_reduce(
            array_reverse($middlewares),
            static fn ($handler, $middleware) => new MiddlewareRequestHandler($middleware, $handler),
            $handler
        )->handle($request);
    }
}
