# AuraRouter

## Methods

### match

```php
<?php

use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\AuraRouter;
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
))->pathOptions([]]);

$router = new AuraRouter([$route]);

/** @var Route $route */
$route = $router->match($request);
```

### generateUrl

```php
<?php

use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\AuraRouter;
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
))->pathOptions([]]);

$router = new AuraRouter([$route]/*, '/path/to/directory'*/);

/** @var string $url */
$url = $router->generateUrl($request, 'index', [], []);
```

### generatePath

```php
<?php

use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\AuraRouter;
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
))->pathOptions([]]);

$router = new AuraRouter([$route]/*, '/path/to/directory'*/);

/** @var string $path */
$path = $router->generatePath('index', [], []);
```

## Route

### Path Options

Supported options:

 * [defaults][10]
 * [host][11]
 * [secure][12]
 * [special][13]
 * [tokens][10]
 * [wildcard][14]

Not supported options:

 * [accepts][20] => 406
 * [allows][21] => handled by abstraction
 * [auth][22] => 401
 * [extras][23] => hidden by abstraction

[10]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#placeholder-tokens-and-default-values
[11]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#host-matching
[12]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#secure-protocols
[13]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#route-specific-matching-logic
[14]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#wildcard-attributes

[20]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#accept-headers
[21]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#multiple-http-verbs
[22]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#authentication
[23]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#custom-extras
