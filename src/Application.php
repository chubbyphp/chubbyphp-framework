<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Framework\Router\RouteMatcherException;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Application
{
    /**
     * @var RouteMatcherInterface
     */
    private $routeMatcher;

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
     * @param RouteMatcherInterface             $routeMatcher
     * @param MiddlewareDispatcherInterface     $middlewareDispatcher
     * @param ExceptionResponseHandlerInterface $exceptionHandler
     * @param LoggerInterface|null              $logger
     */
    public function __construct(
        RouteMatcherInterface $routeMatcher,
        MiddlewareDispatcherInterface $middlewareDispatcher,
        ExceptionResponseHandlerInterface $exceptionHandler,
        LoggerInterface $logger = null
    ) {
        $this->routeMatcher = $routeMatcher;
        $this->middlewareDispatcher = $middlewareDispatcher;
        $this->exceptionHandler = $exceptionHandler;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param ServerRequestInterface $request
     * @param bool                   $send
     *
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request, bool $send = true): ResponseInterface
    {
        try {
            $route = $this->routeMatcher->match($request);
        } catch (RouteMatcherException $routeException) {
            $this->logger->info($routeException->getTitle(), [
                'message' => $routeException->getMessage(),
                'code' => $routeException->getCode(),
            ]);

            $response = $this->exceptionHandler->createRouteMatcherExceptionResponse($request, $routeException);

            if ($send) {
                $this->send($response);
            }

            return $response;
        }

        try {
            $response = $this->middlewareDispatcher->dispatch(
                $route->getMiddlewares(),
                $route->getRequestHandler(),
                $this->requestWithRouteAttributes($request, $route)
            );
        } catch (\Throwable $exception) {
            $this->logger->error('Throwable', ['exceptions' => ExceptionHelper::toArray($exception)]);

            $response = $this->exceptionHandler->createExceptionResponse($request, $exception);
        }

        if ($send) {
            $this->send($response);
        }

        return $response;
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

    /**
     * @param ResponseInterface $response
     */
    private function send(ResponseInterface $response)
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
}
