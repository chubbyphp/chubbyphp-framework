# RouteRequestHandler

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\RequestHandler\RouteRequestHandler;
use Psr\Http\Message\ServerRequestInterface;
use Some\Psr7\Response;
use Some\Psr7\ServerRequest;

$request = new ServerRequest();
$response = new Response();

$middlewareDispatcher = new MiddlewareDispatcher();

$callbackHandler = new RouteRequestHandler($middlewareDispatcher);

$response = $callbackHandler->handle($request);
```
