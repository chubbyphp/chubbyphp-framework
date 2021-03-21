# LazyRequestHandler

## Methods

### handle

```php
<?php

use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Some\Psr11\Container;
use Some\Psr7\ServerRequest;

$request = new ServerRequest();

$container = new Container();

$lazyMiddleware = new LazyRequestHandler($container, 'requestHandler');

$response = $lazyMiddleware->handle($request);
```
