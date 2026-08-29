# ApplicationBuilder

Extends [AbstractCollector](AbstractCollector.md), see there for the inherited `route` / `delete` / `get` / `head` / `options` / `patch` / `post` / `put` / `group` methods.

## Methods

### create

```php
<?php

use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Some\Log\Logger;
use Some\Psr7\ResponseFactory;
use Some\Router\RouteMatcher;
use Some\Router\UrlGenerator;

$middleware = new class() implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
};

$createRouteMatcher = static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes);
$createUrlGenerator = static fn (RoutesByNameInterface $routes) => new UrlGenerator($routes);

/** @var LoggerInterface $logger */
$logger = new Logger();

$applicationBuilder = ApplicationBuilder::create(
    new ResponseFactory(),
    $createRouteMatcher,
    [$middleware],
    $createUrlGenerator,
    true,
    $logger
);
```

### build

```php
<?php

use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ResponseFactory;
use Some\Psr7\ServerRequest;
use Some\Router\RouteMatcher;

$request = new ServerRequest();

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

$createRouteMatcher = static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes);

$applicationBuilder = ApplicationBuilder::create(new ResponseFactory(), $createRouteMatcher);

$app = $applicationBuilder
    ->get('/ping', 'ping', $handler)
    ->build();

$app->emit($app->handle($request));
```
