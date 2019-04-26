<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MiddlewareRequestHandler implements RequestHandlerInterface
{
    /**
     * @var MiddlewareInterface
     */
    private $middleware;

    /**
     * @var RequestHandlerInterface
     */
    private $handler;

    /**
     * @param MiddlewareInterface     $middleware
     * @param RequestHandlerInterface $handler
     */
    public function __construct(MiddlewareInterface $middleware, RequestHandlerInterface $handler)
    {
        $this->middleware = $middleware;
        $this->handler = $handler;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->middleware->process($request, $this->handler);
    }
}
