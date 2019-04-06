<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\ResponseHandler;

use Chubbyphp\Framework\Router\RouteException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ThrowableResponseHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RouteException         $routeException
     *
     * @return ResponseInterface
     */
    public function createRouteExceptionResponse(
        ServerRequestInterface $request,
        RouteException $routeException
    ): ResponseInterface;

    /**
     * @param ServerRequestInterface $request
     * @param \Throwable             $throwable
     *
     * @return ResponseInterface
     */
    public function createThrowableResponse(ServerRequestInterface $request, \Throwable $throwable): ResponseInterface;
}
