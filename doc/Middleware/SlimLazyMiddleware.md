# SlimLazyMiddleware

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\SlimLazyMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr11\Container;
use Some\Psr7\ResponseFactory;
use Some\Psr7\ServerRequest;

$request = new ServerRequest();

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

$container = new Container();
$responseFactory = new ResponseFactory();

$lazyMiddleware = new SlimLazyMiddleware($container, 'middleware', $responseFactory);

$response = $lazyMiddleware->process($request, $handler);
```
