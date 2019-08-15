# ExceptionMiddleware

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var RequestHandlerInterface $handler */
$handler = ...;

$exceptionMiddleware = new ExceptionMiddleware($responseFactory);

/** @var ResponseInterface $response */
$response = $exceptionMiddleware->process($request, $handler);
```
