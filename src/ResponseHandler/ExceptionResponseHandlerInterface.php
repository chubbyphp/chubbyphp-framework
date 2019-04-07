<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\ResponseHandler;

use Chubbyphp\Framework\Router\RouteDispatcherException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ExceptionResponseHandlerInterface
{
    /**
     * @param ServerRequestInterface   $request
     * @param RouteDispatcherException $routeException
     *
     * @return ResponseInterface
     */
    public function createRouteDispatcherExceptionResponse(
        ServerRequestInterface $request,
        RouteDispatcherException $routeException
    ): ResponseInterface;

    /**
     * @param ServerRequestInterface $request
     * @param \Throwable             $exception
     *
     * @return ResponseInterface
     */
    public function createExceptionResponse(ServerRequestInterface $request, \Throwable $exception): ResponseInterface;
}
