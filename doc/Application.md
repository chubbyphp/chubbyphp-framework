# Application

```php
<?php

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ExceptionHandlerInterface;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var RouterInterface $router */
$router = ...;

/** @var MiddlewareDispatcherInterface $middlewareDispatcher */
$middlewareDispatcher = ...;

/** @var ExceptionHandlerInterface $exceptionHandler */
$exceptionHandler = ...;

/** @var ServerRequestInterface $request */
$request = ...;

$app = new Application($router, $middlewareDispatcher, $exceptionHandler);

/** @var ResponseInterface $response */
$response = $app->handle($request);

$app->send($response);
```
