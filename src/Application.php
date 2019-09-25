<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouterException;
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
     * @param array<MiddlewareInterface>         $middlewares
     * @param MiddlewareDispatcherInterface|null $middlewareDispatcher
     */
    public function __construct(
        array $middlewares,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null
    ) {
        $this->middlewareDispatcher = $middlewareDispatcher ?? new MiddlewareDispatcher();

        $this->middlewares = [];
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
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
            new CallbackRequestHandler(function (ServerRequestInterface $request) {
                $route = $request->getAttribute('route');

                if (!$route instanceof RouteInterface) {
                    throw RouterException::createForMissingRouteAttribute($route);
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

    public function send(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        header(sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $statusCode,
            $response->getReasonPhrase()
        ), true, $statusCode);

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        $body = $response->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        while (!$body->eof()) {
            echo $body->read(256);
        }
    }

    private function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }
}
