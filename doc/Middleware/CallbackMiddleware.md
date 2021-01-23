# CallbackMiddleware

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\Middleware\CallbackMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var ResponseInterface $response */
$response = ...;

/** @var RequestHandlerInterface $response */
$handler = ...;

$callbackMiddleware = new CallbackMiddleware(
    static fn (ServerRequestInterface $request, RequestHandlerInterface $handler) => $handler->handle($request)
);

/** @var ResponseInterface $response */
$response = $callbackMiddleware->handle($request, $handler);
```
