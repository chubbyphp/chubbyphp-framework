# RouteRequestHandler

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\RequestHandler\RouteRequestHandler;
use Psr\Http\Message\ServerRequestInterface;
use Some\Psr7\Response;
use Some\Psr7\ServerRequest;

$request = new ServerRequest();
$response = new Response();

$handler = new RouteRequestHandler();

$response = $handler->handle($request);
```
