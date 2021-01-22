# SlimLazyMiddleware

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\SlimLazyMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var ContainerInterface $container */
$container = ...;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$lazyMiddleware = new SlimLazyMiddleware($container, 'middleware', $responseFactory);

/** @var ResponseInterface $response */
$response = $lazyMiddleware->process($request, $handler);
```
