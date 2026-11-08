# Group

## Methods

### create

```php
<?php

use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

$middleware = new class() implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
};

$group = Group::create('/{id}', [
    Group::create('/subgroup', [
        Route::get('/subroute', 'subroute', $handler),
    ]),
    Route::get('/someroute', 'someroute', $handler),
    Route::post('/anotherroute', 'anotherroute', $handler),
], [$middleware], ['requirements' => ['id' => '\d+']]);
```

### getRoutes

```php
<?php

use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

$group = Group::create('/{id}', [
    Route::get('', 'read', $handler),
]);

/** @var array<RouteInterface> $routes */
$routes = $group->getRoutes();
```
