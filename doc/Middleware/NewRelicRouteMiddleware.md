# NewRelicRouteMiddleware

If the new relic php extension is loaded, it calls the function `newrelic_name_transaction` with the route name.

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\NewRelicRouteMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$newRelicRouteMiddleware = new NewRelicRouteMiddleware();

/** @var ResponseInterface $response */
$response = $newRelicRouteMiddleware->process($request, $handler);
```
