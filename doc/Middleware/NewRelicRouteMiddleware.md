# NewRelicRouteMiddleware

If the new relic php extension is loaded, it calls the function `newrelic_name_transaction` with the route name.

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\NewRelicRouteMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\ServerRequest;

$request = new ServerRequest();

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

$newRelicRouteMiddleware = new NewRelicRouteMiddleware();

$response = $newRelicRouteMiddleware->process($request, $handler);
```
