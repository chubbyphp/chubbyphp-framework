# SlimCallbackMiddleware

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\SlimCallbackMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ResponseFactory;
use Some\Psr7\ServerRequest;

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

$response = $callbackMiddleware->process($request, $handler);
```
