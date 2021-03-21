# SlimCallbackMiddleware

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\Middleware\SlimCallbackMiddleware;
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

$responseFactory = new ResponseFactory();

$callbackMiddleware = new SlimCallbackMiddleware(
    static fn (ServerRequestInterface $req, ResponseInterface $res, callable $next) => $next($req, $res),
    $responseFactory
);

$response = $callbackMiddleware->handle($request, $handler);
```
