# Application

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandlerInterface;
use Chubbyphp\Framework\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/** @var RouterInterface $router */
$router = ...;

/** @var MiddlewareDispatcherInterface $middlewareDispatcher */
$middlewareDispatcher = ...;

/** @var ExceptionResponseHandlerInterface $exceptionHandler */
$exceptionHandler = ...;

/** @var LoggerInterface $logger */
$logger = ...;

/** @var ServerRequestInterface $request */
$request = ...;

$app = new Application($router, $middlewareDispatcher, $exceptionHandler, $logger);

/** @var ResponseInterface $response */
$response = $app->handle($request);
```

### send

```php
<?php

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcherInterface;
use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandlerInterface;
use Chubbyphp\Framework\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/** @var RouterInterface $router */
$router = ...;

/** @var MiddlewareDispatcherInterface $middlewareDispatcher */
$middlewareDispatcher = ...;

/** @var ExceptionResponseHandlerInterface $exceptionHandler */
$exceptionHandler = ...;

/** @var LoggerInterface $logger */
$logger = ...;

/** @var ResponseInterface $response */
$response = ...;

$app = new Application($router, $middlewareDispatcher, $exceptionHandler, $logger);
$app->send($response);
```
