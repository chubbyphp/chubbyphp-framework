# SlimCallbackMiddleware

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\Middleware\SlimCallbackMiddleware;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var RequestHandlerInterface $handler */
$handler = ...;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

$callbackMiddleware = new SlimCallbackMiddleware(
    static fn (ServerRequestInterface $req, ResponseInterface $res, callable $next) => $next($req, $res),
    $responseFactory
);

/** @var ResponseInterface $response */
$response = $callbackMiddleware->handle($request, $handler);
```
