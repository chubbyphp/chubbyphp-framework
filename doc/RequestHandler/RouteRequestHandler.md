# RouteRequestHandler

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\RequestHandler\RouteRequestHandler;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ServerRequest;

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

// the route attribute is usually set by the RouteMatcherMiddleware
$request = (new ServerRequest())->withAttribute('route', Route::get('/', 'index', $handler));

$routeRequestHandler = new RouteRequestHandler();

$response = $routeRequestHandler->handle($request);
```
