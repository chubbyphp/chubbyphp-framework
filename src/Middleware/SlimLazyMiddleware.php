<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SlimLazyMiddleware implements MiddlewareInterface
{
    public function __construct(private ContainerInterface $container, private string $id, private ResponseFactoryInterface $responseFactory)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var callable $middleware */
        $middleware = $this->container->get($this->id);

        return (new SlimCallbackMiddleware($middleware, $this->responseFactory))->process($request, $handler);
    }
}
