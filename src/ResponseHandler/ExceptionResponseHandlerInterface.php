<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\ResponseHandler;

use Chubbyphp\Framework\Router\RouterException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ExceptionResponseHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RouterException        $routeException
     *
     * @return ResponseInterface
     */
    public function createRouterExceptionResponse(
        ServerRequestInterface $request,
        RouterException $routeException
    ): ResponseInterface;

    /**
     * @param ServerRequestInterface $request
     * @param \Throwable             $exception
     *
     * @return ResponseInterface
     */
    public function createExceptionResponse(ServerRequestInterface $request, \Throwable $exception): ResponseInterface;
}
