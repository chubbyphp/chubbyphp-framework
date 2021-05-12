<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Chubbyphp\Framework\Router\Exceptions\RouterExceptionInterface;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class RouteMatcherMiddleware implements MiddlewareInterface
{
    private const HTML = <<<'EOT'
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>%s</title>
        <style>
            body {
                margin: 0;
                padding: 30px;
                font: 12px/1.5 Helvetica, Arial, Verdana, sans-serif;
            }

            h1 {
                margin: 0;
                font-size: 48px;
                font-weight: normal;
                line-height: 48px;
            }

            .block {
                margin-bottom: 20px;
            }

            .key {
                width: 100px;
                display: inline-flex;
            }

            .value {
                display: inline-flex;
            }
        </style>
    </head>
    <body>
        %s
    </body>
</html>
EOT;

    private RouteMatcherInterface $routeMatcher;

    private ResponseFactoryInterface $responseFactory;

    private LoggerInterface $logger;

    public function __construct(
        RouteMatcherInterface $routeMatcher,
        ResponseFactoryInterface $responseFactory,
        ?LoggerInterface $logger = null
    ) {
        $this->routeMatcher = $routeMatcher;
        $this->responseFactory = $responseFactory;
        $this->logger = $logger ?? new NullLogger();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $route = $this->routeMatcher->match($request);
        } catch (RouterExceptionInterface $routerException) {
            return $this->routeExceptionResponse($routerException);
        }

        $request = $request->withAttribute('route', $route);

        foreach ($route->getAttributes() as $attribute => $value) {
            $request = $request->withAttribute($attribute, $value);
        }

        return $handler->handle($request);
    }

    private function routeExceptionResponse(RouterExceptionInterface $routerException): ResponseInterface
    {
        $this->logger->info('Route exception', [
            'title' => $routerException->getTitle(),
            'message' => $routerException->getMessage(),
            'code' => $routerException->getCode(),
        ]);

        $response = $this->responseFactory->createResponse($routerException->getCode());
        $response = $response->withHeader('Content-Type', 'text/html');
        $response->getBody()->write(sprintf(
            self::HTML,
            $routerException->getTitle(),
            '<h1>'.$routerException->getTitle().'</h1>'.'<p>'.$routerException->getMessage().'</p>'
        ));

        return $response;
    }
}
