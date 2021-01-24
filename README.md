# chubbyphp-framework

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-framework.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-framework)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-framework/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-framework?branch=master)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/chubbyphp/chubbyphp-framework/master)](https://travis-ci.org/chubbyphp/chubbyphp-framework)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)

[![bugs](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework&metric=bugs)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework)
[![code_smells](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework&metric=code_smells)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework)
[![coverage](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework&metric=coverage)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework)
[![duplicated_lines_density](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework&metric=duplicated_lines_density)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework)
[![ncloc](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework&metric=ncloc)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework)
[![sqale_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework)
[![alert_status](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework&metric=alert_status)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework)
[![reliability_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework)
[![security_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework&metric=security_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework)
[![sqale_index](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework)
[![vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework)

## Description

A based [PSR-15][8] microframework that also sets maximum flexibility with minimum complexity and easy replaceability of the individual components, but also of the framework.
It's currently one of the [fastest PSR-15 based framework (php-fpm)][1] on the market.

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

 * php: ^7.4|^8.0
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
composer require chubbyphp/chubbyphp-framework "^4.0" \
    chubbyphp/chubbyphp-framework-router-aura "^1.0" \
    slim/psr7 "^1.0"
```

[Example][210]

### FastRoute

```bash
composer require chubbyphp/chubbyphp-framework "^4.0" \
    chubbyphp/chubbyphp-framework-router-fastroute "^1.0" \
    slim/psr7 "^1.0"
```

[Example][211]

### SunriseRouter

```bash
composer require chubbyphp/chubbyphp-framework "^4.0" \
    chubbyphp/chubbyphp-framework-router-sunrise "^1.0" \
    slim/psr7 "^1.0"
```

[Example][212]

### Symfony Routing

```bash
composer require chubbyphp/chubbyphp-framework "^4.0" \
    chubbyphp/chubbyphp-framework-router-symfony "^1.0" \
    slim/psr7 "^1.0"
```

[Example][213]

## Usage

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\FastRoute\Router;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

require __DIR__.'/vendor/autoload.php';

$responseFactory = new ResponseFactory();

$app = new Application([
    new ExceptionMiddleware($responseFactory, true),
    new RouterMiddleware(new Router([
        Route::get('/hello/{name:[a-z]+}', 'hello', new CallbackRequestHandler(
            static function (ServerRequestInterface $request) use ($responseFactory) {
                $response = $responseFactory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $request->getAttribute('name')));

                return $response;
            }
        ))
    ]), $responseFactory),
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
 * [SlimCallbackMiddleware][76]
 * [SlimLazyMiddleware][77]

### RequestHandler

 * [CallbackRequestHandler][80]
 * [LazyRequestHandler][81]
 * [SlimCallbackRequestHandler][82]
 * [SlimLazyRequestHandler][83]

### Router

 * [Group][90]
 * [Route][91]

## Server

 * [Builtin (development only)][100]
 * [Nginx][101]
 * [ReactPHP][102]
 * [Roadrunner][103]
 * [Swoole][104]
 * [Workerman][105]

## Skeleton

 * [chubbyphp/chubbyphp-framework-skeleton][200]
 * [chubbyphp/petstore][201]

## Migration

 * [3.x to 4.x][222]
 * [2.x to 3.x][221]
 * [1.x to 2.x][220]
 * [Slim to Chubbyphp][229]

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
[76]: doc/Middleware/SlimCallbackMiddleware.md
[77]: doc/Middleware/SlimLazyMiddleware.md

[80]: doc/RequestHandler/CallbackRequestHandler.md
[81]: doc/RequestHandler/LazyRequestHandler.md
[82]: doc/RequestHandler/SlimCallbackRequestHandler.md
[83]: doc/RequestHandler/SlimLazyRequestHandler.md

[90]: doc/Router/Group.md
[91]: doc/Router/Route.md

[100]: doc/Server/Builtin.md
[101]: doc/Server/Nginx.md
[102]: doc/Server/ReactPHP.md
[103]: doc/Server/Roadrunner.md
[104]: https://github.com/chubbyphp/chubbyphp-swoole-request-handler#usage
[105]: https://github.com/chubbyphp/chubbyphp-workerman-request-handler#usage

[200]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-skeleton
[201]: https://packagist.org/packages/chubbyphp/petstore

[210]: https://github.com/chubbyphp/chubbyphp-framework-router-aura#usage
[211]: https://github.com/chubbyphp/chubbyphp-framework-router-fastroute#usage
[212]: https://github.com/chubbyphp/chubbyphp-framework-router-sunrise#usage
[213]: https://github.com/chubbyphp/chubbyphp-framework-router-symfony#usage

[220]: doc/Migration/1.x-2.x.md
[221]: doc/Migration/2.x-3.x.md
[222]: doc/Migration/3.x-4.x.md
[229]: doc/Migration/Slim-Chubbyphp.md
