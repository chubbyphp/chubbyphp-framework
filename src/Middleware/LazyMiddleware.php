<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyMiddleware implements MiddlewareInterface
{
    public function __construct(private ContainerInterface $container, private string $id)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var MiddlewareInterface $middleware */
        $middleware = $this->container->get($this->id);

        return $middleware->process($request, $handler);
    }
}
