# CallbackRequestHandler

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var ResponseInterface $response */
$response = ...;

$callbackHandler = new CallbackRequestHandler(static fn (ServerRequestInterface $request) => $response);

/** @var ResponseInterface $response */
$response = $callbackHandler->handle($request);
```
