# MiddlewareDispatcher

```php
<?php

use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var MiddlewareInterface $middleware1 */
$middleware1 = ...;

/** @var MiddlewareInterface $middleware2 */
$middleware2 = ...;

/** @var RequestHandlerInterface $handler */
$handler = ...;

/** @var ServerRequestInterface $request */
$request = ...;

$middlewareDispatcher = new MiddlewareDispatcher();

/** @var ResponseInterface $response */
$response = $middlewareDispatcher->dispatch([$middleware1, $middleware2], $handler, $request);
```
