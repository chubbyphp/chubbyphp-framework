<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

use Chubbyphp\Framework\Emitter\Emitter;
use Chubbyphp\Framework\Emitter\EmitterInterface;
use Chubbyphp\Framework\Middleware\PipeMiddleware;
use Chubbyphp\Framework\RequestHandler\RouteRequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Application implements RequestHandlerInterface
{
    private PipeMiddleware $pipeMiddleware;

    private RequestHandlerInterface $routeRequestHandler;

    private EmitterInterface $emitter;

    /**
     * @param array<MiddlewareInterface> $middlewares
     */
    public function __construct(
        array $middlewares,
        ?EmitterInterface $emitter = null
    ) {
        $this->pipeMiddleware = new PipeMiddleware($middlewares);
        $this->routeRequestHandler = new RouteRequestHandler();
        $this->emitter = $emitter ?? new Emitter();
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->pipeMiddleware->process($request, $this->routeRequestHandler);
    }

    public function emit(ResponseInterface $response): void
    {
        $this->emitter->emit($response);
    }
}
