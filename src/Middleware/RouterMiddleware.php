<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * @deprecated use RouteMatcherMiddleware
 */
final class RouterMiddleware implements MiddlewareInterface
{
    private RouteMatcherMiddleware $RouteMatcherMiddleware;

    public function __construct(
        RouteMatcherInterface $routeMatcher,
        ResponseFactoryInterface $responseFactory,
        ?LoggerInterface $logger = null
    ) {
        @trigger_error(
            sprintf('Use %s parameter instead of instead of "%s"', RouteMatcherMiddleware::class, self::class),
            E_USER_DEPRECATED
        );

        $this->RouteMatcherMiddleware = new RouteMatcherMiddleware($routeMatcher, $responseFactory, $logger);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->RouteMatcherMiddleware->process($request, $handler);
    }
}
