# chubbyphp-framework

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-framework.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-framework)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-framework/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-framework?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)

## Description

A minimal middleware based micro framework using [PHP Framework Interop Group - PSR][1], where the goal is to achieve
the best combination of flexibility and simplicity by using standards.

 * [Basic Coding Standard (1)][2]
 * [Coding Style Guide (2)][3]
 * [Logger Interface (3)][4]
 * [Autoloading Standard (4)][5]
 * [HTTP Message Interface (7)][6]
 * [Container Interface (11)][7]
 * [HTTP Handlers (15)][8]
 * [HTTP Factories (17)][9]

![Application workflow](doc/Resources/workflow.png?raw=true "Application workflow")

[Framework Benchmark][10]

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
 * [nikic/fast-route][31]: ^1.0|^0.6
 * [sunrise/http-router][32]: ^2.0
 * [symfony/routing][33]: ^4.3|^5.0

### PSR 7 / PSR 17

 * [bittyphp/http][40]: ^2.0
 * [guzzlehttp/psr7][41]: ^1.4.2 (with [http-interop/http-factory-guzzle][42]: ^1.0)
 * [nyholm/psr7][43]: ^1.0
 * [slim/psr7][44]: ^0.5|^1.0
 * [sunrise/http-message][45]: ^1.0 (with [sunrise/http-factory][46]: ^1.0)
 * [laminas/laminas-diactoros][47]: ^2.0

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-framework][60].

## Usage

### Aura.Router

```bash
composer require chubbyphp/chubbyphp-framework "^2.8" aura/router "^3.1" slim/psr7 "^1.0"
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
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

set_error_handler([new ErrorHandler(), 'errorToException']);

$responseFactory = new ResponseFactory();

$app = new Application([
    new ExceptionMiddleware($responseFactory, true),
    new RouterMiddleware(new AuraRouter([
        Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($responseFactory) {
                $name = $request->getAttribute('name');
                $response = $responseFactory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ))->pathOptions([AuraRouter::PATH_TOKENS => ['name' => '[a-z]+']])
    ]), $responseFactory),
]);

$app->emit($app->handle((new ServerRequestFactory())->createFromGlobals()));
```

### FastRoute

```bash
composer require chubbyphp/chubbyphp-framework "^2.8" nikic/fast-route "^1.3" slim/psr7 "^1.0"
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
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

set_error_handler([new ErrorHandler(), 'errorToException']);

$responseFactory = new ResponseFactory();

$app = new Application([
    new ExceptionMiddleware($responseFactory, true),
    new RouterMiddleware(new FastRouteRouter([
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

### SunriseRouter

```bash
composer require chubbyphp/chubbyphp-framework "^2.8" sunrise/http-router "^2.1" slim/psr7 "^1.0"
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
use Chubbyphp\Framework\Router\SunriseRouter;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

set_error_handler([new ErrorHandler(), 'errorToException']);

$responseFactory = new ResponseFactory();

$app = new Application([
    new ExceptionMiddleware($responseFactory, true),
    new RouterMiddleware(new SunriseRouter([
        Route::get('/hello/{name<[a-z]+>}', 'hello', new CallbackRequestHandler(
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

### Symfony Routing

```bash
composer require chubbyphp/chubbyphp-framework "^2.8" symfony/routing "^5.0" slim/psr7 "^1.0"
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
use Chubbyphp\Framework\Router\SymfonyRouter;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

set_error_handler([new ErrorHandler(), 'errorToException']);

$responseFactory = new ResponseFactory();

$app = new Application([
    new ExceptionMiddleware($responseFactory, true),
    new RouterMiddleware(new SymfonyRouter([
        Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($responseFactory) {
                $name = $request->getAttribute('name');
                $response = $responseFactory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ))->pathOptions([SymfonyRouter::PATH_REQUIREMENTS => ['name' => '[a-z]+']])
    ]), $responseFactory),
]);

$app->emit($app->handle((new ServerRequestFactory())->createFromGlobals()));
```

#### Advanced example with Middleware before and after routing

This is an example of middleware(s) before and after the routing was done.

If you need to be able to continue without finding a route, I recommend writing a RouterMiddleware that will pass either
the route or the RouteException and at the end another middleware that will convert the RouteException to a http response.

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
use Chubbyphp\Framework\Router\FastRouteRouter;
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
        new FastRouteRouter([
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

 * [AuraRouter][90]
 * [FastRouteRouter][91]
 * [SunriseRouter][92]
 * [Group][93]
 * [Route][94]

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

[1]: https://www.php-fig.org/psr/

[2]: https://www.php-fig.org/psr/psr-1
[3]: https://www.php-fig.org/psr/psr-2
[4]: https://www.php-fig.org/psr/psr-3
[5]: https://www.php-fig.org/psr/psr-4
[6]: https://www.php-fig.org/psr/psr-7
[7]: https://www.php-fig.org/psr/psr-11
[8]: https://www.php-fig.org/psr/psr-15
[9]: https://www.php-fig.org/psr/psr-17

[10]: https://github.com/the-benchmarker/web-frameworks#results

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
[32]: https://packagist.org/packages/sunrise/http-router
[33]: https://packagist.org/packages/symfony/routing

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

[90]: doc/Router/AuraRouter.md
[91]: doc/Router/FastRouteRouter.md
[92]: doc/Router/SunriseRouter.md
[93]: doc/Router/Group.md
[94]: doc/Router/Route.md

[100]: doc/Webserver/Builtin.md
[101]: doc/Webserver/Nginx.md

[200]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-skeleton
[201]: https://packagist.org/packages/chubbyphp/petstore

[210]: https://packagist.org/packages/chubbyphp/chubbyphp-swoole-request-handler
