# Route

## Methods

### create

```php
<?php

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

$route = Route::delete('/{id}', 'delete', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### get

```php
<?php

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

$route = Route::get('/{id}', 'read', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### head

```php
<?php

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

$route = Route::head('/{id}', 'head', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### options

```php
<?php

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

$route = Route::options('/{id}', 'options', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### patch

```php
<?php

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

$route = Route::patch('/{id}', 'patch', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### post

```php
<?php

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

$route = Route::post('/{id}', 'post', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### put

```php
<?php

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

$route = Route::put('/{id}', 'put', $handler, [$middleware], ['requirements' => ['id' => '\d+']]);
```

### getName

```php
<?php

use Chubbyphp\Framework\Router\Route;
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

$route = Route::get('/', 'get', $handler);

$name = $route->getName();
```

### getMethod

```php
<?php

use Chubbyphp\Framework\Router\Route;
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

$route = Route::get('/', 'get', $handler);

$method = $route->getMethod();
```

### getPath

```php
<?php

use Chubbyphp\Framework\Router\Route;
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

$route = Route::get('/', 'get', $handler);

$path = $route->getPath();
```

### getPathOptions

```php
<?php

use Chubbyphp\Framework\Router\Route;
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

$route = Route::get('/', 'get', $handler);

/** @var array<string, mixed> $pathOptions */
$pathOptions = $route->getPathOptions();
```

### getMiddlewares

```php
<?php

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

$route = Route::get('/', 'get', $handler);

/** @var array<MiddlewareInterface> $middlewares */
$middlewares = $route->getMiddlewares();
```

### getRequestHandler

```php
<?php

use Chubbyphp\Framework\Router\Route;
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

$route = Route::get('/', 'get', $handler);

$handler = $route->getRequestHandler();
```

### withAttributes

```php
<?php

use Chubbyphp\Framework\Router\Route;
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

$route = Route::get('/', 'get', $handler);

$route = $route->withAttributes(['id' => 'afe339cb-d099-4091-9ad6-38c46d6578fe']);
```

### getAttributes

```php
<?php

use Chubbyphp\Framework\Router\Route;
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

$route = Route::get('/', 'get', $handler);

/** @var array<string, string> $attributes */
$attributes = $route->getAttributes();
```
