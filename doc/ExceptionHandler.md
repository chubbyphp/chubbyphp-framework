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
use Psr\Log\NullLogger;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

$logger = new NullLogger();

/** @var ServerRequestInterface $request */
$request = ...;

/** @var RouterException $routeException */
$routeException = ...;

$exceptionResponseHandler = new ExceptionHandler($responseFactory);

/** @var ResponseInterface $response */
$response = $exceptionResponseHandler->createRouterExceptionResponse($request, $routeException);
```

### createExceptionResponse

```php
<?php

use Chubbyphp\Framework\ExceptionHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

$logger = new NullLogger();

/** @var ServerRequestInterface $request */
$request = ...;

$exception = new \Exception('sample');

$exceptionResponseHandler = new ExceptionHandler($responseFactory, $logger);

/** @var ResponseInterface $response */
$response = $exceptionResponseHandler->createExceptionResponse($request, $exception);
```
