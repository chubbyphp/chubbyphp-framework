<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

use Chubbyphp\Framework\Emitter\Emitter;
use Chubbyphp\Framework\Emitter\EmitterInterface;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\RequestHandler\RouteRequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Application implements RequestHandlerInterface
{
    /**
     * @var array<MiddlewareInterface>
     */
    private array $middlewares;

    private MiddlewareDispatcherInterface $middlewareDispatcher;

    private RequestHandlerInterface $requestHandler;

    private EmitterInterface $emitter;

    /**
     * @param array<MiddlewareInterface> $middlewares
     */
    public function __construct(
        array $middlewares,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null,
        ?RequestHandlerInterface $requestHandler = null,
        ?EmitterInterface $emitter = null
    ) {
        $this->middlewares = [];
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }

        $this->middlewareDispatcher = $middlewareDispatcher ?? new MiddlewareDispatcher();
        $this->requestHandler = $requestHandler ?? new RouteRequestHandler($this->middlewareDispatcher);
        $this->emitter = $emitter ?? new Emitter();
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->middlewareDispatcher->dispatch(
            $this->middlewares,
            $this->requestHandler,
            $request
        );
    }

    public function emit(ResponseInterface $response): void
    {
        $this->emitter->emit($response);
    }

    private function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }
}
