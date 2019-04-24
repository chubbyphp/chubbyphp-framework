<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\ResponseHandler;

use Chubbyphp\Framework\Router\RouteMatcherException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ExceptionResponseHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RouteMatcherException  $routeException
     *
     * @return ResponseInterface
     */
    public function createRouteMatcherExceptionResponse(
        ServerRequestInterface $request,
        RouteMatcherException $routeException
    ): ResponseInterface;

    /**
     * @param ServerRequestInterface $request
     * @param \Throwable             $exception
     *
     * @return ResponseInterface
     */
    public function createExceptionResponse(ServerRequestInterface $request, \Throwable $exception): ResponseInterface;
}
