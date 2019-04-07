<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\ResponseHandler;

use Chubbyphp\Framework\Router\RouteException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ExceptionResponseHandlerInterface
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
     * @param \Throwable             $exception
     *
     * @return ResponseInterface
     */
    public function createExceptionResponse(ServerRequestInterface $request, \Throwable $exception): ResponseInterface;
}
