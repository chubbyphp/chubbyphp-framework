<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Chubbyphp\Framework\Router\Exceptions\RouterException;
use Chubbyphp\HttpException\HttpException;
use Psr\Http\Message\ServerRequestInterface;

interface RouteMatcherInterface
{
    /**
     * @throws HttpException|RouterException
     */
    public function match(ServerRequestInterface $request): RouteInterface;
}
