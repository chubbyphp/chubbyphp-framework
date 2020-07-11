# Group

## Methods

### create

```php
<?php

use Chubbyphp\Framework\Router\Group;
use Psr\Http\Server\MiddlewareInterface;

/** @var MiddlewareInterface $middleware */
$middleware = ...;

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
