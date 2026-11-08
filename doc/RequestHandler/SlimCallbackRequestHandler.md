# SlimCallbackRequestHandler

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\RequestHandler\SlimCallbackRequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Some\Psr7\ResponseFactory;
use Some\Psr7\ServerRequest;

$request = new ServerRequest();

$responseFactory = new ResponseFactory();

$callbackHandler = new SlimCallbackRequestHandler(
    static fn (ServerRequestInterface $req, ResponseInterface $res, array $args) => $res,
    $responseFactory
);

$response = $callbackHandler->handle($request);
```
