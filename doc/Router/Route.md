# Route

## Methods

### create

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$route = Route::create(Route::GET, '/api/pets', 'pet_list', $handler);
```

### delete

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$route = Route::delete('/api/pets/{id}', 'pet_delete', $handler);
```

### get

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$route = Route::get('/api/pets/{id}', 'pet_read', $handler);
```

### head

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$route = Route::head('/api/pets/{id}', 'pet_read_header', $handler);
```

### options

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$route = Route::options('/api/pets/{id}', 'pet_options', $handler);
```

### patch

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$route = Route::patch('/api/pets/{id}', 'pet_update', $handler);
```

### post

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$route = Route::post('/api/pets/{id}', 'pet_create', $handler);
```

### put

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\RequestHandlerInterface;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$route = Route::put('/api/pets/{id}', 'pet_replace', $handler);
```

### pathOptions

```php
<?php

use Chubbyphp\Framework\Router\Route;

/** @var Route $route */
$route = ...;

$route->pathOptions([]);
```

### middlewares

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\MiddlewareInterface;

/** @var MiddlewareInterface $middleware */
$middleware = ...;

/** @var Route $route */
$route = ...;

$route->middlewares([$middleware]);
```

### middleware

```php
<?php

use Chubbyphp\Framework\Router\Route;
use Psr\Http\Server\MiddlewareInterface;

/** @var MiddlewareInterface $middleware */
$middleware = ...;

/** @var Route $route */
$route = ...;

$route->middleware($middleware);
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

/** @var MiddlewareInterface[] $middlewares */
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

$route->withAttributes(['id' => 'afe339cb-d099-4091-9ad6-38c46d6578fe']);
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

### __toString

```php
<?php

use Chubbyphp\Framework\Router\Route;

/** @var Route $route */
$route = ...;

(string) $route;
```
