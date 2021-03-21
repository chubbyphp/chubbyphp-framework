# MiddlewareDispatcher

## Methods

### dispatch

```php
<?php

use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\ServerRequest;

$request = new ServerRequest();

$middleware1 = new class() implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
};

$middleware2 = new class() implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
};

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

$middlewareDispatcher = new MiddlewareDispatcher();

$response = $middlewareDispatcher->dispatch([$middleware1, $middleware2], $handler, $request);
```
