# RouteMatcherMiddleware

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\Router\Some\RouteMatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\ServerRequest;
use Some\Psr7\ResponseFactory;

$request = new ServerRequest();

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

$routeMatcher = new RouteMatcher();
$responseFactory = new ResponseFactory();

$RouteMatcherMiddleware = new RouteMatcherMiddleware($routeMatcher, $responseFactory);

$response = $RouteMatcherMiddleware->process($request, $handler);
```
