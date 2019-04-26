# LazyMiddleware

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var ContainerInterface $container */
$container = ...;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$lazyMiddleware = new LazyMiddleware($container, 'middleware');

/** @var ResponseInterface $response */
$response = $lazyMiddleware->process($request, $handler);
```
