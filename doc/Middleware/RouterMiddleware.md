# RouterMiddleware

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\Router\RouterInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var ResponseFactoryInterface $router */
$router = ...;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$routerMiddleware = new RouterMiddleware($router, $responseFactory);

/** @var ResponseInterface $response */
$response = $routerMiddleware->process($request, $handler);
```
