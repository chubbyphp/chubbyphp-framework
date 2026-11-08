# RouteMatcherMiddleware

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\RequestHandler\RouteRequestHandler;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RoutesByName;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ServerRequest;
use Some\Router\RouteMatcher;

$request = new ServerRequest();

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

$routeMatcher = new RouteMatcher(new RoutesByName([
    Route::get('/', 'index', $handler),
]));

$routeMatcherMiddleware = new RouteMatcherMiddleware($routeMatcher);

$response = $routeMatcherMiddleware->process($request, new RouteRequestHandler());
```
