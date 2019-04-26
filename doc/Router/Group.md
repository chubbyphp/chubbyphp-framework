# Group

## Methods

### create

```php
<?php

use Chubbyphp\Framework\Router\Group;

$group = Group::create('/api');
```

### pathOptions

```php
<?php

use Chubbyphp\Framework\Router\Group;

$group = Group::create('/api');
$group->pathOptions([]);
```

### middlewares

```php
<?php

use Chubbyphp\Framework\Router\Group;
use Psr\Http\Server\MiddlewareInterface;

/** @var MiddlewareInterface $middleware */
$middleware = ...;

$group = Group::create('/api');
$group->middlewares([$middleware]);
```

### middleware

```php
<?php

use Chubbyphp\Framework\Router\Group;
use Psr\Http\Server\MiddlewareInterface;

/** @var MiddlewareInterface $middleware */
$middleware = ...;

$group = Group::create('/api');
$group->middleware($middleware);
```

### group

```php
<?php

use Chubbyphp\Framework\Router\Group;

$group = Group::create('/api');
$group->group(Group::create('/pets'));
```

### route

```php
<?php

use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\RouteInterface;

/** @var RouteInterface $route */
$route = ...;

$group = Group::create('/api');
$group->route($route);
```

### getRoutes

```php
<?php

use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\RouteInterface;

/** @var RouteInterface $route1 */
$route1 = ...;

/** @var RouteInterface $route2 */
$route2 = ...;

/** @var RouteInterface $route3 */
$route3 = ...;

$group = Group::create('/api');
$group->group(Group::create('/pets')->route($route1)->route($route2));
$group->route($route3);

/** @var RouteInterface[] $routes */
$routes = $group->getRoutes();

$route1 = $routes[0];
$route2 = $routes[1];
$route3 = $routes[2];
```
