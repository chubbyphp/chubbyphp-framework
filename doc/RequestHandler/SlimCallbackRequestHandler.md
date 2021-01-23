# SlimCallbackRequestHandler

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\RequestHandler\SlimCallbackRequestHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = ...;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

$callbackHandler = new SlimCallbackRequestHandler(
    static fn (ServerRequestInterface $req, ResponseInterface $res, array $args) => $res,
    $responseFactory
);

/** @var ResponseInterface $response */
$response = $callbackHandler->handle($request);
```
