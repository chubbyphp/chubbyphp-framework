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
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $id;

    /**
     * @param ContainerInterface $container
     * @param string             $id
     */
    public function __construct(ContainerInterface $container, string $id)
    {
        $this->container = $container;
        $this->id = $id;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->container->get($this->id)->process($request, $handler);
    }
}
