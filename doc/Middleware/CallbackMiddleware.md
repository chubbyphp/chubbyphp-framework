# CallbackMiddleware

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\Middleware\CallbackMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ServerRequest;

$request = new ServerRequest();

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

$callbackMiddleware = new CallbackMiddleware(
    static fn (ServerRequestInterface $request, RequestHandlerInterface $handler) => $handler->handle($request)
);

$response = $callbackMiddleware->handle($request, $handler);
```
