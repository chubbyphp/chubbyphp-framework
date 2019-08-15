# chubbyphp-framework

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-framework.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-framework)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-framework/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-framework/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-framework/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-framework/?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)

## Description

A minimal middleware based micro framework using [PHP Framework Interop Group - PSR][1], with the goal is to achive
the best combination of flexibility and simplicity by using standards.

 * [Basic Coding Standard (1)][2]
 * [Coding Style Guide (2)][3]
 * [Logger Interface (3)][4]
 * [Autoloading Standard (4)][5]
 * [HTTP Message Interface (7)][6]
 * [Container Interface (11)][7]
 * [HTTP Handlers (15)][8]
 * [HTTP Factories (17)][9]

## Requirements

 * php: ^7.2
 * [psr/container][20]: ^1.0
 * [psr/http-factory][21]: ^1.0.1
 * [psr/http-message-implementation][22]: ^1.0
 * [psr/http-message][23]: ^1.0.1
 * [psr/http-server-handler][24]: ^1.0.1
 * [psr/http-server-middleware][25]: ^1.0.1
 * [psr/log][25]: ^1.1

## Suggest

### Router

Any Router which implements `Chubbyphp\Framework\Router\RouterInterface` can be used.

 * [aura/router][30]: ^3.1
 * [nikic/fast-route][31]: ^1.3

### PSR 7 / PSR 17

 * [guzzlehttp/psr7][32]: ^1.4.2 (with [http-interop/http-factory-guzzle][33]: ^1.0)
 * [nyholm/psr7][34]: ^1.0
 * [slim/psr7][35]: ^0.4
 * [zendframework/zend-diactoros][36]: ^2.0

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-framework][40].

## Usage

### Aura.Router

```bash
composer require chubbyphp/chubbyphp-framework "^2.0" \
    aura/router "^3.1" zendframework/zend-diactoros "^2.0"
```

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ErrorHandler;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\AuraRouter;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

set_error_handler([ErrorHandler::class, 'handle']);

$responseFactory = new ResponseFactory();

$route = Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
    function (ServerRequestInterface $request) use ($responseFactory) {
        $name = $request->getAttribute('name');
        $response = $responseFactory->createResponse();
        $response->getBody()->write(sprintf('Hello, %s', $name));

        return $response;
    }
))->pathOptions(['tokens' => ['name' => '[a-z]+']]);

$app = new Application([
    new ExceptionMiddleware($responseFactory, true),
    new RouterMiddleware(new AuraRouter([$route]), $responseFactory),
]);

$app->send($app->handle(ServerRequestFactory::fromGlobals()));
```

### FastRoute

```bash
composer require chubbyphp/chubbyphp-framework "^2.0" \
    nikic/fast-route "^1.3" zendframework/zend-diactoros "^2.0"
```

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ErrorHandler;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

set_error_handler([ErrorHandler::class, 'handle']);

$responseFactory = new ResponseFactory();

$route = Route::get('/hello/{name:[a-z]+}', 'hello', new CallbackRequestHandler(
    function (ServerRequestInterface $request) use ($responseFactory) {
        $name = $request->getAttribute('name');
        $response = $responseFactory->createResponse();
        $response->getBody()->write(sprintf('Hello, %s', $name));

        return $response;
    }
));

$app = new Application([
    new ExceptionMiddleware($responseFactory, true),
    new RouterMiddleware(new FastRouteRouter([$route]), $responseFactory),
]);


$app->send($app->handle(ServerRequestFactory::fromGlobals()));
```

### Middleware

 * [ExceptionMiddleware][70]
 * [LazyMiddleware][71]
 * [MiddlewareDispatcher][72]
 * [RouterMiddleware][73]

### RequestHandler

 * [CallbackRequestHandler][80]
 * [LazyRequestHandler][81]

### Router

 * [AuraRouter][90]
 * [FastRouteRouter][91]
 * [Group][92]
 * [Route][93]

## Webserver

 * [Builtin (development only)][100]
 * [Nginx][101]

## Skeleton

 * [chubbyphp/chubbyphp-framework-skeleton][200]
 * [chubbyphp/petstore][201]

## Copyright

Dominik Zogg 2019

[1]: https://www.php-fig.org/psr/

[2]: https://www.php-fig.org/psr/psr-1
[3]: https://www.php-fig.org/psr/psr-2
[4]: https://www.php-fig.org/psr/psr-3
[5]: https://www.php-fig.org/psr/psr-4
[6]: https://www.php-fig.org/psr/psr-7
[7]: https://www.php-fig.org/psr/psr-11
[8]: https://www.php-fig.org/psr/psr-15
[9]: https://www.php-fig.org/psr/psr-17

[15]: https://travis-ci.org/chubbyphp/chubbyphp-framework

[20]: https://packagist.org/packages/psr/container
[21]: https://packagist.org/packages/psr/http-factory
[22]: https://packagist.org/packages/psr/http-message-implementation
[23]: https://packagist.org/packages/psr/http-message
[24]: https://packagist.org/packages/psr/http-server-handler
[25]: https://packagist.org/packages/psr/http-server-middleware
[26]: https://packagist.org/packages/psr/log

[30]: https://packagist.org/packages/aura/router
[31]: https://packagist.org/packages/nikic/fast-route

[32]: https://packagist.org/packages/guzzlehttp/psr7
[33]: https://packagist.org/packages/http-interop/http-factory-guzzle
[34]: https://packagist.org/packages/nyholm/psr7
[35]: https://packagist.org/packages/slim/psr7
[36]: https://packagist.org/packages/zendframework/zend-diactoros

[40]: https://packagist.org/packages/chubbyphp/chubbyphp-framework

[70]: doc/Middleware/ExceptionMiddleware.md
[71]: doc/Middleware/LazyMiddleware.md
[72]: doc/Middleware/MiddlewareDispatcher.md
[73]: doc/Middleware/RouterMiddleware.md

[80]: doc/RequestHandler/CallbackRequestHandler.md
[81]: doc/RequestHandler/LazyRequestHandler.md

[90]: doc/Router/AuraRouter.md
[91]: doc/Router/FastRouteRouter.md
[92]: doc/Router/Group.md
[93]: doc/Router/Route.md

[100]: doc/Webserver/Builtin.md
[101]: doc/Webserver/Nginx.md

[200]:https://packagist.org/packages/chubbyphp/chubbyphp-framework-skeleton
[201]:https://packagist.org/packages/chubbyphp/petstore
