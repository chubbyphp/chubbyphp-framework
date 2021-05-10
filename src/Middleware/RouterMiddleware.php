<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Chubbyphp\Framework\Router\UrlMatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * @deprecated
 */
final class RouterMiddleware implements MiddlewareInterface
{
    private UrlMatcherMiddleware $urlMatcherMiddleware;

    public function __construct(
        UrlMatcherInterface $urlMatcher,
        ResponseFactoryInterface $responseFactory,
        ?LoggerInterface $logger = null
    ) {
        @trigger_error(
            sprintf('Use %s parameter instead of instead of "%s"', UrlMatcherMiddleware::class, self::class),
            E_USER_DEPRECATED
        );

        $this->urlMatcherMiddleware = new UrlMatcherMiddleware($urlMatcher, $responseFactory, $logger);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->urlMatcherMiddleware->process($request, $handler);
    }
}
