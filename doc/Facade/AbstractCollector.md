# AbstractCollector

Base class of `ApplicationBuilder` and `GroupCollector`, the examples below use `ApplicationBuilder`.

## Methods

### route

```php
<?php

use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ResponseFactory;
use Some\Router\RouteMatcher;

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

$createRouteMatcher = static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes);

$applicationBuilder = ApplicationBuilder::create(new ResponseFactory(), $createRouteMatcher);

$applicationBuilder = $applicationBuilder->route(
    'TRACE',
    '/{id}',
    'trace',
    [$middleware],
    $handler,
    ['requirements' => ['id' => '\d+']]
);
```

### delete

```php
<?php

use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ResponseFactory;
use Some\Router\RouteMatcher;

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

$createRouteMatcher = static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes);

$applicationBuilder = ApplicationBuilder::create(new ResponseFactory(), $createRouteMatcher);

$applicationBuilder = $applicationBuilder->delete(
    '/{id}',
    'delete',
    [$middleware],
    $handler,
    ['requirements' => ['id' => '\d+']]
);
```

### get

```php
<?php

use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ResponseFactory;
use Some\Router\RouteMatcher;

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

$createRouteMatcher = static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes);

$applicationBuilder = ApplicationBuilder::create(new ResponseFactory(), $createRouteMatcher);

$applicationBuilder = $applicationBuilder->get(
    '/{id}',
    'get',
    [$middleware],
    $handler,
    ['requirements' => ['id' => '\d+']]
);
```

### head

```php
<?php

use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ResponseFactory;
use Some\Router\RouteMatcher;

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

$createRouteMatcher = static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes);

$applicationBuilder = ApplicationBuilder::create(new ResponseFactory(), $createRouteMatcher);

$applicationBuilder = $applicationBuilder->head(
    '/{id}',
    'head',
    [$middleware],
    $handler,
    ['requirements' => ['id' => '\d+']]
);
```

### options

```php
<?php

use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ResponseFactory;
use Some\Router\RouteMatcher;

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

$createRouteMatcher = static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes);

$applicationBuilder = ApplicationBuilder::create(new ResponseFactory(), $createRouteMatcher);

$applicationBuilder = $applicationBuilder->options(
    '/{id}',
    'options',
    [$middleware],
    $handler,
    ['requirements' => ['id' => '\d+']]
);
```

### patch

```php
<?php

use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ResponseFactory;
use Some\Router\RouteMatcher;

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

$createRouteMatcher = static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes);

$applicationBuilder = ApplicationBuilder::create(new ResponseFactory(), $createRouteMatcher);

$applicationBuilder = $applicationBuilder->patch(
    '/{id}',
    'patch',
    [$middleware],
    $handler,
    ['requirements' => ['id' => '\d+']]
);
```

### post

```php
<?php

use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ResponseFactory;
use Some\Router\RouteMatcher;

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

$createRouteMatcher = static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes);

$applicationBuilder = ApplicationBuilder::create(new ResponseFactory(), $createRouteMatcher);

$applicationBuilder = $applicationBuilder->post(
    '/{id}',
    'post',
    [$middleware],
    $handler,
    ['requirements' => ['id' => '\d+']]
);
```

### put

```php
<?php

use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ResponseFactory;
use Some\Router\RouteMatcher;

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

$createRouteMatcher = static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes);

$applicationBuilder = ApplicationBuilder::create(new ResponseFactory(), $createRouteMatcher);

$applicationBuilder = $applicationBuilder->put(
    '/{id}',
    'put',
    [$middleware],
    $handler,
    ['requirements' => ['id' => '\d+']]
);
```

### group

```php
<?php

use Chubbyphp\Framework\Facade\ApplicationBuilder;
use Chubbyphp\Framework\Facade\GroupCollector;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ResponseFactory;
use Some\Router\RouteMatcher;

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

$createRouteMatcher = static fn (RoutesByNameInterface $routes) => new RouteMatcher($routes);

$applicationBuilder = ApplicationBuilder::create(new ResponseFactory(), $createRouteMatcher);

$applicationBuilder = $applicationBuilder->group(
    '/{id}',
    [$middleware],
    static fn (GroupCollector $group) => $group
        ->get('', 'read', $handler)
        ->put('', 'update', [$middleware], $handler)
        ->group('/sub', static fn (GroupCollector $sub) => $sub->get('', 'sub_read', $handler)),
    ['requirements' => ['id' => '\d+']]
);
```

