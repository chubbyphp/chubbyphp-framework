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

### Sample using Diactoros, FastRoute and Pimple

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandler;
use Chubbyphp\Framework\Router\FastRoute\RouteDispatcher;
use Chubbyphp\Framework\Router\FastRoute\UrlGenerator;
use Chubbyphp\Framework\Router\RouteCollection;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Pimple\Container;
use Pimple\Psr11\Container as Psr11Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

$container = new Container();
$psrContainer = new Psr11Container($container);

$container[ResponseFactory::class] = function () {
    return new ResponseFactory();
};

$container[RouteCollection::class] = function () use ($psrContainer) {
    return (new RouteCollection())
        ->route('/', RouteInterface::GET, 'index', new LazyRequestHandler($psrContainer, 'requestHandler'));
};

$container[UrlGenerator::class] = function () use ($container) {
    return new UrlGenerator($container[RouteCollection::class]);
};

$container['requestHandler'] = function () use ($container) {
    return new class(
        $container[ResponseFactory::class],
        $container[UrlGenerator::class]
    ) implements RequestHandlerInterface {
        /**
         * @var ResponseFactoryInterface
         */
        private $responseFactory;

        /**
         * @var UrlGeneratorInterface
         */
        private $urlGenerator;

        /**
         * @param ResponseFactoryInterface $responseFactory
         * @param UrlGeneratorInterface    $urlGenerator
         */
        public function __construct(
            ResponseFactoryInterface $responseFactory,
            UrlGeneratorInterface $urlGenerator
        ) {
            $this->responseFactory = $responseFactory;
            $this->urlGenerator = $urlGenerator;
        }

        /**
         * @param ServerRequestInterface $request
         *
         * @return ResponseInterface
         */
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            $response = $this->responseFactory->createResponse();
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(
                json_encode(['url' => $this->urlGenerator->requestTargetFor('index')])
            );

            return $response;
        }
    };
};

$app = new Application(
    new RouteDispatcher($container[RouteCollection::class]),
    new MiddlewareDispatcher(),
    new ExceptionResponseHandler($container[ResponseFactory::class])
);

$app->run(ServerRequestFactory::fromGlobals());
```

## Copyright

Dominik Zogg 2019

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-framework
