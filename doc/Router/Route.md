# Route

## Methods

### create

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\MidlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

/** @var MidlewareInterface $middleware */
$middleware = ...;

$route = Route::create(
    Route::GET,
    '/{id}',
    'list',
    $handler,
    [$middleware],
    ['requirements' => ['id' => '\d+']]
);
```

### delete

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\MidlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

/** @var MidlewareInterface $middleware */
$middleware = ...;

$route = Route::delete('/{id}', 'delete', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### get

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\MidlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

/** @var MidlewareInterface $middleware */
$middleware = ...;

$route = Route::get('/{id}', 'read', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### head

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\MidlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

/** @var MidlewareInterface $middleware */
$middleware = ...;

$route = Route::head('/{id}', 'head', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### options

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\MidlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

/** @var MidlewareInterface $middleware */
$middleware = ...;

$route = Route::options('/{id}', 'options', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### patch

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\MidlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

/** @var MidlewareInterface $middleware */
$middleware = ...;

$route = Route::patch('/{id}', 'patch', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### post

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\MidlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

/** @var MidlewareInterface $middleware */
$middleware = ...;

$route = Route::post('/{id}', 'post', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### put

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\MidlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

/** @var MidlewareInterface $middleware */
$middleware = ...;

$route = Route::put('/{id}', 'put', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### getName

```php
<?php

use Chubbyphp\Framework\Router\Route;

/** @var Route $route */
$route = ...;

/** @var string $name */
$name = $route->getName();
```

### getMethod

```php
<?php

use Chubbyphp\Framework\Router\Route;

/** @var Route $route */
$route = ...;

/** @var string $method */
$method = $route->getMethod();
```

### getPath

```php
<?php

use Chubbyphp\Framework\Router\Route;

/** @var Route $route */
$route = ...;

/** @var string $path */
$path = $route->getPath();
```

### getPathOptions

```php
<?php

use Chubbyphp\Framework\Router\Route;

/** @var Route $route */
$route = ...;

/** @var array $pathOptions */
$pathOptions = $route->getPathOptions();
```

### getMiddlewares

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\MiddlewareInterface;

/** @var Route $route */
$route = ...;

/** @var array<MiddlewareInterface> $middlewares */
$middlewares = $route->getMiddlewares();
```

### getRequestHandler

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\RequestHandlerInterface;

/** @var Route $route */
$route = ...;

/** @var RequestHandlerInterface $handler */
$handler = $route->getRequestHandler();
```

### withAttributes

```php
<?php

use Chubbyphp\Framework\Router\Route;

/** @var Route $route */
$route = ...;

$route = $route->withAttributes(['id' => 'afe339cb-d099-4091-9ad6-38c46d6578fe']);
```

### getAttributes

```php
<?php

use Chubbyphp\Framework\Router\Route;

/** @var Route $route */
$route = ...;

/** @var array $attributes */
$attributes = $route->getAttributes();
```
