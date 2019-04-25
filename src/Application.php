<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandlerInterface;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouterException;
use Chubbyphp\Framework\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
     * @var ExceptionResponseHandlerInterface
     */
    private $exceptionHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RouterInterface                   $router
     * @param MiddlewareDispatcherInterface     $middlewareDispatcher
     * @param ExceptionResponseHandlerInterface $exceptionHandler
     * @param LoggerInterface|null              $logger
     */
    public function __construct(
        RouterInterface $router,
        MiddlewareDispatcherInterface $middlewareDispatcher,
        ExceptionResponseHandlerInterface $exceptionHandler,
        LoggerInterface $logger = null
    ) {
        $this->router = $router;
        $this->middlewareDispatcher = $middlewareDispatcher;
        $this->exceptionHandler = $exceptionHandler;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $route = $this->router->match($request);
        } catch (RouterException $routeException) {
            $this->logger->info($routeException->getTitle(), [
                'message' => $routeException->getMessage(),
                'code' => $routeException->getCode(),
            ]);

            return $this->exceptionHandler->createRouterExceptionResponse($request, $routeException);
        }

        try {
            return $this->middlewareDispatcher->dispatch(
                $route->getMiddlewares(),
                $route->getRequestHandler(),
                $this->requestWithRouteAttributes($request, $route)
            );
        } catch (\Throwable $exception) {
            $this->logger->error('Throwable', ['exceptions' => ExceptionHelper::toArray($exception)]);

            return $this->exceptionHandler->createExceptionResponse($request, $exception);
        }
    }

    /**
     * @param ResponseInterface $response
     */
    public function send(ResponseInterface $response)
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

    /**
     * @param ServerRequestInterface $request
     * @param RouteInterface         $route
     *
     * @return ServerRequestInterface
     */
    private function requestWithRouteAttributes(
        ServerRequestInterface $request,
        RouteInterface $route
    ): ServerRequestInterface {
        $request = $request->withAttribute('route', $route);
        foreach ($route->getAttributes() as $attribute => $value) {
            $request = $request->withAttribute($attribute, $value);
        }

        return $request;
    }
}
