# CallbackRequestHandler

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Psr\Http\Message\ServerRequestInterface;
use Some\Psr7\Response;
use Some\Psr7\ServerRequest;

$request = new ServerRequest();
$response = new Response();

$callbackHandler = new CallbackRequestHandler(static fn (ServerRequestInterface $request) => $response);

$response = $callbackHandler->handle($request);
```
