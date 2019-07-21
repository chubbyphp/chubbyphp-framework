<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\RouterException;
use Chubbyphp\Framework\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Application implements RequestHandlerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var MiddlewareDispatcherInterface
     */
    private $middlewareDispatcher;

    /**
     * @var ExceptionHandlerInterface
     */
    private $exceptionHandler;

    /**
     * @var array<MiddlewareInterface>
     */
    private $middlewares;

    /**
     * @param RouterInterface               $router
     * @param MiddlewareDispatcherInterface $middlewareDispatcher
     * @param ExceptionHandlerInterface     $exceptionHandler
     * @param array<MiddlewareInterface>    $middlewares
     */
    public function __construct(
        RouterInterface $router,
        MiddlewareDispatcherInterface $middlewareDispatcher,
        ExceptionHandlerInterface $exceptionHandler,
        array $middlewares = []
    ) {
        $this->router = $router;
        $this->middlewareDispatcher = $middlewareDispatcher;
        $this->exceptionHandler = $exceptionHandler;

        $this->middlewares = [];
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            if ([] === $this->middlewares) {
                return $this->routeAndDispatch($request);
            }

            return $this->middlewareDispatcher->dispatch(
                $this->middlewares,
                new CallbackRequestHandler(function (ServerRequestInterface $request) {
                    return $this->routeAndDispatch($request);
                }),
                $request
            );
        } catch (\Throwable $exception) {
            return $this->exceptionHandler->createExceptionResponse($request, $exception);
        }
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

    private function routeAndDispatch(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $route = $this->router->match($request);
        } catch (RouterException $routeException) {
            return $this->exceptionHandler->createRouterExceptionResponse($request, $routeException);
        }

        // @deprecated remove this line in v2
        $request = $request->withAttribute('route', $route);

        foreach ($route->getAttributes() as $attribute => $value) {
            $request = $request->withAttribute($attribute, $value);
        }

        return $this->middlewareDispatcher->dispatch(
            $route->getMiddlewares(),
            $route->getRequestHandler(),
            $request
        );
    }
}
