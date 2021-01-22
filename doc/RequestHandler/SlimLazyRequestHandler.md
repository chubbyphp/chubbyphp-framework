# SlimLazyRequestHandler

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\RequestHandler\SlimLazyRequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var ContainerInterface $container */
$container = ...;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

/** @var ServerRequestInterface $request */
$request = ...;

$lazyMiddleware = new SlimLazyRequestHandler($container, 'requestHandler', $responseFactory);

/** @var ResponseInterface $response */
$response = $lazyMiddleware->handle($request);
```
