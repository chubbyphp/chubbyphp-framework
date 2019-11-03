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
        if ([] === $middlewares) {
            return $handler->handle($request);
        }

        $this->validateMiddlewares($middlewares);

        $middlewares = array_reverse($middlewares);

        /** @var MiddlewareInterface $firstMiddleware */
        $firstMiddleware = array_pop($middlewares);

        foreach ($middlewares as $middleware) {
            $handler = new MiddlewareRequestHandler($middleware, $handler);
        }

        return $firstMiddleware->process($request, $handler);
    }

    /**
     * @param array<MiddlewareInterface> $middlewares
     */
    private function validateMiddlewares(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof MiddlewareInterface) {
                throw new \TypeError(
                    sprintf(
                        '%s::dispatch() expects parameter 1 to be %s[], %s[] given',
                        self::class,
                        MiddlewareInterface::class,
                        get_class($middleware)
                    )
                );
            }
        }
    }
}
