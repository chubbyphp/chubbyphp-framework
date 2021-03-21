# LazyMiddleware

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr11\Container;
use Some\Psr7\Response;
use Some\Psr7\ServerRequest;

$request = new ServerRequest();

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

$container = new Container();

$lazyMiddleware = new LazyMiddleware($container, 'middleware');

$response = $lazyMiddleware->process($request, $handler);
```
