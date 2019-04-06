<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface MiddlewareDispatcherInterface
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
    ): ResponseInterface;
}
