<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\Router\RouteDispatcherInterface;
use Chubbyphp\Framework\Router\RouteException;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Application
{
    /**
     * @var RouteDispatcherInterface
     */
    private $routeDispatcher;

    /**
     * @var MiddlewareDispatcherInterface
     */
    private $middlewareDispatcher;

    /**
     * @var ExceptionResponseHandlerInterface
     */
    private $throwableHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RouteDispatcherInterface          $routeDispatcher
     * @param MiddlewareDispatcherInterface     $middlewareDispatcher
     * @param ExceptionResponseHandlerInterface $throwableHandler
     * @param LoggerInterface|null              $logger
     */
    public function __construct(
        RouteDispatcherInterface $routeDispatcher,
        MiddlewareDispatcherInterface $middlewareDispatcher,
        ExceptionResponseHandlerInterface $throwableHandler,
        LoggerInterface $logger = null
    ) {
        $this->routeDispatcher = $routeDispatcher;
        $this->middlewareDispatcher = $middlewareDispatcher;
        $this->throwableHandler = $throwableHandler;
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
            $route = $this->routeDispatcher->dispatch($request);
            $response = $this->middlewareDispatcher->dispatch(
                $route->getMiddlewares(),
                $route->getRequestHandler(),
                $this->requestWithRouteAttributes($request, $route)
            );
        } catch (RouteException $routeException) {
            $this->logger->info($routeException->getTitle(), [
                'message' => $routeException->getMessage(),
                'code' => $routeException->getCode(),
            ]);

            $response = $this->throwableHandler->createRouteExceptionResponse($request, $routeException);
        } catch (\Throwable $throwable) {
            $this->logger->error('Throwable', ['throwables' => $this->nestedThrowableToArray($throwable)]);

            $response = $this->throwableHandler->createExceptionResponse($request, $throwable);
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
        $request = $request->withAttribute('_route', $route->getName());
        foreach ($route->getAttributes() as $attribute => $value) {
            $request = $request->withAttribute($attribute, $value);
        }

        return $request;
    }

    /**
     * @param \Throwable $throwable
     *
     * @return array
     */
    private function nestedThrowableToArray(\Throwable $throwable): array
    {
        $throwables = [];
        do {
            $throwables[] = $this->throwableToArray($throwable);
        } while ($throwable = $throwable->getPrevious());

        return $throwables;
    }

    /**
     * @param \Throwable $throwable
     *
     * @return array
     */
    private function throwableToArray(\Throwable $throwable): array
    {
        return [
            'message' => $throwable->getMessage(),
            'code' => $throwable->getCode(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => $throwable->getTraceAsString(),
        ];
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
