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
     * @param MiddlewareInterface[]   $middlewares
     * @param RequestHandlerInterface $requestHandler
     * @param ServerRequestInterface  $request
     *
     * @return ResponseInterface
     */
    public function dispatch(
        array $middlewares,
        RequestHandlerInterface $requestHandler,
        ServerRequestInterface $request
    ): ResponseInterface {
        if ([] === $middlewares) {
            return $requestHandler->handle($request);
        }

        $middlewares = array_values($middlewares);

        $this->validateMiddlewares($middlewares);

        krsort($middlewares);

        $firstMiddleware = array_pop($middlewares);

        foreach ($middlewares as $middleware) {
            $requestHandler = new MiddlewareRequestHandler($middleware, $requestHandler);
        }

        return $firstMiddleware->process($request, $requestHandler);
    }

    /**
     * @param MiddlewareInterface[] $middlewares
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
                        is_object($middleware) ? get_class($middleware) : gettype($middleware)
                    )
                );
            }
        }
    }
}
