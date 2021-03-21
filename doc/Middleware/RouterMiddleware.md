# RouterMiddleware

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\Router\Some\Router;
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

$router = new Router();
$responseFactory = new ResponseFactory();

$routerMiddleware = new RouterMiddleware($router, $responseFactory);

$response = $routerMiddleware->process($request, $handler);
```
