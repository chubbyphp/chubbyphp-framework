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

/** @var Group $group */
$group = ...;

$group->pathOptions([]);
```

### middlewares

```php
<?php

use Chubbyphp\Framework\Router\Group;
use Psr\Http\Server\MiddlewareInterface;

/** @var MiddlewareInterface $middleware */
$middleware = ...;

/** @var Group $group */
$group = ...;

$group->middlewares([$middleware]);
```

### middleware

```php
<?php

use Chubbyphp\Framework\Router\Group;
use Psr\Http\Server\MiddlewareInterface;

/** @var MiddlewareInterface $middleware */
$middleware = ...;

/** @var Group $group */
$group = ...;

$group->middleware($middleware);
```

### group

```php
<?php

use Chubbyphp\Framework\Router\Group;

/** @var Group $group */
$group = ...;

/** @var Group $group */
$subGroup = ...;

$group->group($subGroup);
```

### route

```php
<?php

use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\RouteInterface;

/** @var RouteInterface $route */
$route = ...;

/** @var Group $group */
$group = ...;

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

/** @var Group $group */
$subGroup = ...;

$subGroup->route($route1);
$subGroup->route($route2);

/** @var Group $group */
$group = ...;

$group->group($subGroup);
$group->route($route3);

/** @var RouteInterface[] $routes */
$routes = $group->getRoutes();

$route1 = $routes[0];
$route2 = $routes[1];
$route3 = $routes[2];
```
