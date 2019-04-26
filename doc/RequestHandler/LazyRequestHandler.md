# LazyRequestHandler

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var ContainerInterface $container */
$container = ...;

/** @var ServerRequestInterface $request */
$request = ...;

$lazyMiddleware = new LazyRequestHandler($container, 'requestHandler');

/** @var ResponseInterface $response */
$response = $lazyMiddleware->handle($request);
```
