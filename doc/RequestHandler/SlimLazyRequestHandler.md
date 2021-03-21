# SlimLazyRequestHandler

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\RequestHandler\SlimLazyRequestHandler;
use Some\Psr11\Container;
use Some\Psr7\ResponseFactory;
use Some\Psr7\ServerRequest;

$request = new ServerRequest();

$container = new Container();
$responseFactory = new ResponseFactory();

$lazyMiddleware = new SlimLazyRequestHandler($container, 'requestHandler', $responseFactory);

$response = $lazyMiddleware->handle($request);
```
