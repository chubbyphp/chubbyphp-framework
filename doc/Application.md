# Application

```php
<?php

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ExceptionHandlerInterface;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

/** @var RouterInterface $router */
$router = ...;

/** @var MiddlewareDispatcherInterface $middlewareDispatcher */
$middlewareDispatcher = ...;

/** @var ExceptionHandlerInterface $exceptionHandler */
$exceptionHandler = ...;

/** @var MiddlewareInterface $routeIndependMiddleware */
$routeIndependMiddleware = ...;

/** @var ServerRequestInterface $request */
$request = ...;

$app = new Application($router, $middlewareDispatcher, $exceptionHandler, [$routeIndependMiddleware]);

/** @var ResponseInterface $response */
$response = $app->handle($request);

$app->send($response);
```
