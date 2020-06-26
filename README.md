# chubbyphp-framework

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-framework.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-framework)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-framework/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-framework?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)

## Description

A based [PSR-15][8] microframework that also sets maximum flexibility with minimum complexity and easy replaceability of the individual components, but also of the framework.
Although performance was not a focus, it's currently the [fastest PSR-15 based framework (php-fpm)][1] on the market.

 * [Basic Coding Standard (1)][2]
 * [Coding Style Guide (2)][3]
 * [Logger Interface (3)][4]
 * [Autoloading Standard (4)][5]
 * [HTTP Message Interface (7)][6]
 * [Container Interface (11)][7]
 * [HTTP Handlers (15)][8]
 * [HTTP Factories (17)][9]

![Application workflow](doc/Resources/workflow.png?raw=true "Application workflow")

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

 * [chubbyphp/chubbyphp-framework-router-aura][30]: ^1.0
 * [chubbyphp/chubbyphp-framework-router-fastroute][31]: ^1.0
 * [chubbyphp/chubbyphp-framework-router-sunrise][32]: ^1.0
 * [chubbyphp/chubbyphp-framework-router-symfony][33]: ^1.0

### PSR 7 / PSR 17

 * [bittyphp/http][40]: ^2.0
 * [guzzlehttp/psr7][41]: ^1.4.2 (with [http-interop/http-factory-guzzle][42]: ^1.0)
 * [nyholm/psr7][43]: ^1.0
 * [slim/psr7][44]: ^0.5|^1.0
 * [sunrise/http-message][45]: ^1.0 (with [sunrise/http-factory][46]: ^1.0)
 * [laminas/laminas-diactoros][47]: ^2.0

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-framework][60].

### Aura.Router

```bash
composer require chubbyphp/chubbyphp-framework "^3.0" \
    chubbyphp/chubbyphp-framework-router-aura "^1.0" \
    slim/psr7 "^1.0"
```

[Example][220]

### FastRoute

```bash
composer require chubbyphp/chubbyphp-framework "^3.0" \
    chubbyphp/chubbyphp-framework-router-fastroute "^1.0" \
    slim/psr7 "^1.0"
```

[Example][221]

### SunriseRouter

```bash
composer require chubbyphp/chubbyphp-framework "^3.0" \
    chubbyphp/chubbyphp-framework-router-sunrise "^1.0" \
    slim/psr7 "^1.0"
```

[Example][222]

### Symfony Routing

```bash
composer require chubbyphp/chubbyphp-framework "^3.0" \
    chubbyphp/chubbyphp-framework-router-symfony "^1.0" \
    slim/psr7 "^1.0"
```

[Example][223]

## Usage

### Fastroute

#### Basic

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ErrorHandler;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\FastRoute\Router;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

set_error_handler([new ErrorHandler(), 'errorToException']);

$responseFactory = new ResponseFactory();

$app = new Application([
    new ExceptionMiddleware($responseFactory, true),
    new RouterMiddleware(new Router([
        Route::get('/hello/{name:[a-z]+}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($responseFactory) {
                $name = $request->getAttribute('name');
                $response = $responseFactory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ))
    ]), $responseFactory),
]);

$app->emit($app->handle((new ServerRequestFactory())->createFromGlobals()));
```

#### Advanced

This is an example of middleware(s) before and after the routing was done.

If you need to be able to continue without finding a route, I recommend writing a RouterMiddleware that will pass either the route or the RouteException and at the end another middleware that will convert the RouteException to a http response.

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ErrorHandler;
use Chubbyphp\Framework\Middleware\CallbackMiddleware;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\FastRoute\Router;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

set_error_handler([new ErrorHandler(), 'errorToException']);

$responseFactory = new ResponseFactory();

$app = new Application([
    new ExceptionMiddleware($responseFactory, true),
    new CallbackMiddleware(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        return $handler->handle($request);
    }),
    new RouterMiddleware(
        new Router([
            Route::get('/hello/{name:[a-z]+}', 'hello', new CallbackRequestHandler(
                function (ServerRequestInterface $request) use ($responseFactory) {
                    $name = $request->getAttribute('name');
                    $response = $responseFactory->createResponse();
                    $response->getBody()->write(sprintf('Hello, %s', $name));

                    return $response;
                }
            ))
        ]),
        $responseFactory
    ),
    new CallbackMiddleware(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        /** @var Route $route */
        $route = $request->getAttribute('route');

        if ('hello' === $route->getName()) {
            $request = $request->withAttribute('name', 'world');
        }

        return $handler->handle($request);
    }),
]);

$app->emit($app->handle((new ServerRequestFactory())->createFromGlobals()));
```

### Emitter

 * [Emitter][65]

### Middleware

 * [CallbackMiddleware][70]
 * [ExceptionMiddleware][71]
 * [LazyMiddleware][72]
 * [MiddlewareDispatcher][73]
 * [NewRelicRouteMiddleware][74]
 * [RouterMiddleware][75]

### RequestHandler

 * [CallbackRequestHandler][80]
 * [LazyRequestHandler][81]

### Router

 * [Group][90]
 * [Route][91]

## Webserver

 * [Builtin (development only)][100]
 * [Nginx][101]

## Skeleton

 * [chubbyphp/chubbyphp-framework-skeleton][200]
 * [chubbyphp/petstore][201]

## Application Server

### ReactPHP

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use React\EventLoop\Factory;
use React\Http\Server;
use React\Socket\Server as Socket;

/** @var Application $app*/
$app = ...;

$loop = Factory::create();

$socket = new Socket(8080, $loop);

$server = new Server($app);
$server->listen($socket);

$loop->run();
```

### Roadrunner

```php
<?php

namespace App;

use Chubbyphp\Framework\Application;
use Spiral\Goridge\StreamRelay;
use Spiral\RoadRunner\Worker;
use Spiral\RoadRunner\PSR7Client;

ini_set('display_errors', 'stderr');

/** @var Application $app */
$app = ...;

$worker = new Worker(new StreamRelay(STDIN, STDOUT));
$psr7 = new PSR7Client($worker);

while ($req = $psr7->acceptRequest()) {
    try {
        $psr7->respond($app->handle($req));
    } catch (\Throwable $e) {
        $psr7->getWorker()->error((string)$e);
    }
}
```

### Swoole

 * [chubbyphp/chubbyphp-swoole-request-handler][210]

## Migration

### From 2.x to 3.x

#### Aura.Router

1. Run the new installation guide
2. Replace `Chubbyphp\Framework\Router\AuraRouter` with `Chubbyphp\Framework\Router\Aura\Router`.

#### FastRoute

1. Run the new installation guide
2. Replace `Chubbyphp\Framework\Router\FastRouteRouter` with `Chubbyphp\Framework\Router\FastRoute\Router`.

#### SunriseRouter

1. Run the new installation guide
2. Replace `Chubbyphp\Framework\Router\SunriseRouter` with `Chubbyphp\Framework\Router\Sunrise\Router`.

#### Symfony Routing

1. Run the new installation guide
2. Replace `Chubbyphp\Framework\Router\SymfonyRouter` with `Chubbyphp\Framework\Router\Symfony\Router`.

### From 1.x to 2.x

Replace the code from the first block with the code of the second ones.

```php
use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ExceptionHandler;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\Router\FastRouteRouter;

$app = new Application(
    new FastRouteRouter([$route]),
    new MiddlewareDispatcher(),
    new ExceptionHandler($responseFactory, true)
);
```

```php
use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\Router\FastRouteRouter;

$app = new Application([
    new ExceptionMiddleware($responseFactory, true),
    new RouterMiddleware(new FastRouteRouter([$route]), $responseFactory),
]);
```

## Copyright

Dominik Zogg 2020

[1]: https://github.com/the-benchmarker/web-frameworks#results

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

[30]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-aura
[31]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-fastroute
[32]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-sunrise
[33]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-symfony

[40]: https://packagist.org/packages/bittyphp/http
[41]: https://packagist.org/packages/guzzlehttp/psr7
[42]: https://packagist.org/packages/http-interop/http-factory-guzzle
[43]: https://packagist.org/packages/nyholm/psr7
[44]: https://packagist.org/packages/slim/psr7
[45]: https://packagist.org/packages/sunrise/http-message
[46]: https://packagist.org/packages/sunrise/http-factory
[47]: https://packagist.org/packages/laminas/laminas-diactoros

[60]: https://packagist.org/packages/chubbyphp/chubbyphp-framework

[65]: doc/Emitter/Emitter.md

[70]: doc/Middleware/CallbackMiddleware.md
[71]: doc/Middleware/ExceptionMiddleware.md
[72]: doc/Middleware/LazyMiddleware.md
[73]: doc/Middleware/MiddlewareDispatcher.md
[74]: doc/Middleware/NewRelicRouteMiddleware.md
[75]: doc/Middleware/RouterMiddleware.md

[80]: doc/RequestHandler/CallbackRequestHandler.md
[81]: doc/RequestHandler/LazyRequestHandler.md

[90]: doc/Router/Group.md
[91]: doc/Router/Route.md

[100]: doc/Webserver/Builtin.md
[101]: doc/Webserver/Nginx.md

[200]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-skeleton
[201]: https://packagist.org/packages/chubbyphp/petstore

[210]: https://packagist.org/packages/chubbyphp/chubbyphp-swoole-request-handler

[220]: https://github.com/chubbyphp/chubbyphp-framework-router-aura#usage
[221]: https://github.com/chubbyphp/chubbyphp-framework-router-fastroute#usage
[222]: https://github.com/chubbyphp/chubbyphp-framework-router-sunrise#usage
[223]: https://github.com/chubbyphp/chubbyphp-framework-router-symfony#usage
