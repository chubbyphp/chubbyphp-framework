<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

use Chubbyphp\Framework\Emitter\Emitter;
use Chubbyphp\Framework\Emitter\EmitterInterface;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\Exceptions\MissingRouteAttributeOnRequestException;
use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Application implements RequestHandlerInterface
{
    /**
     * @var array<MiddlewareInterface>
     */
    private $middlewares;

    /**
     * @var MiddlewareDispatcherInterface
     */
    private $middlewareDispatcher;

    /**
     * @var EmitterInterface
     */
    private $emitter;

    /**
     * @param array<MiddlewareInterface> $middlewares
     */
    public function __construct(
        array $middlewares,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null,
        ?EmitterInterface $emitter = null
    ) {
        $this->middlewares = [];
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }

        $this->middlewareDispatcher = $middlewareDispatcher ?? new MiddlewareDispatcher();
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
            new CallbackRequestHandler(function (ServerRequestInterface $request) {
                $route = $request->getAttribute('route');

                if (!$route instanceof RouteInterface) {
                    throw MissingRouteAttributeOnRequestException::create($route);
                }

                return $this->middlewareDispatcher->dispatch(
                    $route->getMiddlewares(),
                    $route->getRequestHandler(),
                    $request
                );
            }),
            $request
        );
    }

    public function emit(ResponseInterface $response): void
    {
        $this->emitter->emit($response);
    }

    /**
     * @deprecated 3.0
     */
    public function send(ResponseInterface $response): void
    {
        @trigger_error('Use emit instead', E_USER_DEPRECATED);

        $this->emit($response);
    }

    private function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }
}
