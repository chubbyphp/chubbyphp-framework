<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class PipeMiddleware implements MiddlewareInterface
{
    /**
     * @var array<MiddlewareInterface>
     */
    private readonly array $middlewares;

    /**
     * @param array<MiddlewareInterface> $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->middlewares = array_reverse($middlewares);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $reducedHandler = $handler;
        foreach ($this->middlewares as $middleware) {
            $reducedHandler = new MiddlewareRequestHandler($middleware, $reducedHandler);
        }

        return $reducedHandler->handle($request);
    }
}
