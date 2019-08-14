# ExceptionHandler

## Methods

### createRouterExceptionResponse

```php
<?php

use Chubbyphp\Framework\ExceptionHandler;
use Chubbyphp\Framework\Router\RouterException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var RouterException $routerException */
$routerException = ...;

$exceptionResponseHandler = new ExceptionHandler($responseFactory);

/** @var ResponseInterface $response */
$response = $exceptionResponseHandler->createRouterExceptionResponse($request, $routerException);
```

### createExceptionResponse

```php
<?php

use Chubbyphp\Framework\ExceptionHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

/** @var ServerRequestInterface $request */
$request = ...;

$exception = new \Exception('sample');

$exceptionResponseHandler = new ExceptionHandler($responseFactory);

/** @var ResponseInterface $response */
$response = $exceptionResponseHandler->createExceptionResponse($request, $exception);
```
