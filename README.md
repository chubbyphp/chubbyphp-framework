# chubbyphp-framework

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-framework.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-framework)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-framework/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-framework/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-framework/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-framework/?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-framework)

## Description

A minimal Framework using PSR 3, PSR 7, PSR 11, PSR 15 and PSR 17.

## Requirements

 * php: ^7.2
 * psr/container: ^1.0
 * psr/http-factory: ^1.0
 * psr/http-message: ^1.0.1
 * psr/http-server-middleware: ^1.0.1
 * psr/log: ^1.1

## Suggest

 * nikic/fast-route: ^1.3
 * pimple/pimple: ^3.2.3
 * zendframework/zend-diactoros: ^2.1.1

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-framework][1].

```sh
composer require chubbyphp/chubbyphp-framework "^1.0"
```

## Usage

### Basic Sample using Diactoros and FastRoute

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\ResponseHandler\HtmlExceptionResponseHandler;
use Chubbyphp\Framework\Router\FastRoute\RouteDispatcher;
use Chubbyphp\Framework\Router\RouteCollection;
use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface as PsrRequestHandlerInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

$responseFactory = new ResponseFactory();

$routeCollection = new RouteCollection();
$routeCollection
    ->route(
        '/hello/{name}',
        RouteInterface::GET,
        'hello',
        new class($responseFactory) implements PsrRequestHandlerInterface
        {
            /**
             * @var ResponseFactoryInterface
             */
            private $responseFactory;

            /**
             * @param string $responseFactory
             */
            public function __construct(string $responseFactory)
            {
                $this->responseFactory = $responseFactory;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $name = $request->getAttribute('name');
                $response = $this->responseFactory->createResponse();
                $response->getBody()->write("Hello, $name");

                return $response;
            }
        }
    );

$app = new Application(
    new RouteDispatcher($routeCollection),
    new MiddlewareDispatcher(),
    new HtmlExceptionResponseHandler($responseFactory)
);

$app->run(ServerRequestFactory::fromGlobals());
```

## Copyright

Dominik Zogg 2019

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-framework
