<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

use Chubbyphp\Framework\Router\RouterException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ExceptionHandlerInterface
{
    public function createRouterExceptionResponse(
        ServerRequestInterface $request,
        RouterException $routeException
    ): ResponseInterface;

    public function createExceptionResponse(ServerRequestInterface $request, \Throwable $exception): ResponseInterface;
}
