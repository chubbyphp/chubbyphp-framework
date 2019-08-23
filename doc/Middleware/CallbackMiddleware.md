# CallbackMiddleware

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\Middleware\CallbackMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var ResponseInterface $response */
$response = ...;

/** @var RequestHandlerInterface $response */
$handler = ...;

$callbackMiddleware = new CallbackMiddleware(
    function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        return $handler->handle($request);
    }
);

/** @var ResponseInterface $response */
$response = $callbackMiddleware->handle($request, $handler);
```
