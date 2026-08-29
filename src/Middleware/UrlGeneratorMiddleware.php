<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class UrlGeneratorMiddleware implements MiddlewareInterface
{
    public const string ATTRIBUTE = 'urlGenerator';

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request->withAttribute(self::ATTRIBUTE, $this->urlGenerator));
    }
}
