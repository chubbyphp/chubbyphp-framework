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
     * @param array<MiddlewareInterface>                    $middlewares
     * @param RequestHandlerInterface|EmitterInterface|null $requestHandler
     */
    public function __construct(
        array $middlewares,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null,
        $requestHandler = null,
        ?EmitterInterface $emitter = null
    ) {
        $this->middlewares = [];
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }

        $this->middlewareDispatcher = $middlewareDispatcher ?? new MiddlewareDispatcher();

        $this->requestHandler = new RouteRequestHandler($this->middlewareDispatcher);
        $this->emitter = new Emitter();

        if ($requestHandler instanceof RequestHandlerInterface) {
            $this->requestHandler = $requestHandler;
        } elseif ($requestHandler instanceof EmitterInterface) {
            @trigger_error('$emitter should be provided as 4 instead of 3 parameter', E_USER_DEPRECATED);

            $this->emitter = $requestHandler;
        }

        if (null !== $emitter) {
            $this->emitter = $emitter;
        }
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
