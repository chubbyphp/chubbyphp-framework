# FastRouteRouter

## Methods

### match

```php
<?php

use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var ResponseInterface $response */
$response = ...;

$route = Route::get('/', 'index', new CallbackRequestHandler(
    function (ServerRequestInterface $request) use ($response) {
        return $response;
    }
));

$router = new FastRouteRouter([$route]);

/** @var Route $route */
$route = $router->match($request);
```

### generateUrl

```php
<?php

use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var ResponseInterface $response */
$response = ...;

$route = Route::get('/', 'index', new CallbackRequestHandler(
    function (ServerRequestInterface $request) use ($response) {
        return $response;
    }
));

$router = new FastRouteRouter([$route]/*, '/path/to/cache', '/path/to/directory'*/);

/** @var string $url */
$url = $router->generateUrl($request, 'index', [], []);
```

### generatePath

```php
<?php

use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var ResponseInterface $response */
$response = ...;

$route = Route::get('/', 'index', new CallbackRequestHandler(
    function (ServerRequestInterface $request) use ($response) {
        return $response;
    }
));

$router = new FastRouteRouter([$route]/*, '/path/to/cache', '/path/to/directory'*/);

/** @var string $path */
$path = $router->generatePath('index', [], []);
```
