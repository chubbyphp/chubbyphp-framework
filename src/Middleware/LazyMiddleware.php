<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Chubbyphp\Framework\Debug;
use Chubbyphp\Framework\DebugInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyMiddleware implements DebugInterface, MiddlewareInterface
{
    private ContainerInterface $container;

    private string $id;

    public function __construct(ContainerInterface $container, string $id)
    {
        $this->container = $container;
        $this->id = $id;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $middleware = $this->container->get($this->id);
        if (!$middleware instanceof MiddlewareInterface) {
            throw new \TypeError(
                sprintf(
                    '%s() expects service with id "%s" to be %s, %s given',
                    __METHOD__,
                    $this->id,
                    MiddlewareInterface::class,
                    is_object($middleware) ? get_class($middleware) : gettype($middleware)
                )
            );
        }

        return $middleware->process($request, $handler);
    }

    public function debug(): array
    {
        return Debug::debug([
            'class' => self::class,
            'container' => $this->container,
            'id' => $this->id,
        ]);
    }
}
