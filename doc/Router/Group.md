# Group

## Methods

### create

```php
<?php

use Chubbyphp\Framework\Router\Group;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

$middleware = new class() implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
};

$group = Group::create('/{id}', [
    new Group('/subgroup'),
    new Route('/someroute'),
    new Route('/anotherroute')
], [$middleware], ['requirements' => ['id' => '\d+']]);
```

### getRoutes

```php
<?php

use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\RouteInterface;

$group = Group::create();

/** @var array<RouteInterface> $routes */
$routes = $group->getRoutes();
```
