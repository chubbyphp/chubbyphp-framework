# ExceptionResponseHandler

## Methods

### createRouterExceptionResponse

```php
<?php

use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandler;
use Chubbyphp\Framework\Router\RouterException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var RouterException $routeException */
$routeException = ...;

$exceptionResponseHandler = new ExceptionResponseHandler($responseFactory);

/** @var ResponseInterface $response */
$response = $exceptionResponseHandler->createRouterExceptionResponse($request, $routeException);
```

### createExceptionResponse

```php
<?php

use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

/** @var ServerRequestInterface $request */
$request = ...;

$exception = new \Exception('sample');

$exceptionResponseHandler = new ExceptionResponseHandler($responseFactory);

/** @var ResponseInterface $response */
$response = $exceptionResponseHandler->createExceptionResponse($request, $exception);
```
