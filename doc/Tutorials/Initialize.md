# Initialize

## Create

```bash
mkdir /path/to/my/project
cd /path/to/my/project
```

## Create .gitignore

```
cd /path/to/my/project
printf "vendor\n" > .gitignore
```

## Composer

```bash
cd /path/to/my/project
composer require chubbyphp/chubbyphp-framework "^1.0@alpha,>=1.0-alpha8" \
    nikic/fast-route "^1.3" zendframework/zend-diactoros "^2.1.2"
```

## Create app

```bash
cd /path/to/my/project
mkdir app
```

## Add autoload section to composer.json

```json
{
    ...
    "autoload": {
        "psr-4": { "App\\": "app/" }
    }
    ...
}
```

```bash
composer dump-autoload
```

## Create app/bootstrap.php

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\ErrorHandler;

$loader = require __DIR__.'/../vendor/autoload.php';

set_error_handler([ErrorHandler::class, 'handle']);
```

## Create app/app.php

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ExceptionHandler;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ResponseFactory;

require __DIR__.'/bootstrap.php';

$responseFactory = new ResponseFactory();

$route = Route::get('/ping', 'ping', new CallbackRequestHandler(
    function (ServerRequestInterface $request) use ($responseFactory) {
        $response = $responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Expires', '0');

        $response->getBody()->write(json_encode(['datetime' => date(\DateTime::ATOM)]));

        return $response;
    }
));

$app = new Application(
    new FastRouteRouter([$route]),
    new MiddlewareDispatcher(),
    new ExceptionHandler($responseFactory, true)
);

return $app;
```

## Create public

```bash
cd /path/to/my/project
mkdir public
```

## Create public/index.php

```php
<?php

declare(strict_types=1);

use Zend\Diactoros\ServerRequestFactory;

$app = require __DIR__.'/../app/app.php';

$app->send($app->handle(ServerRequestFactory::fromGlobals()));
```

## Test the application

```bash
cd /path/to/my/project
php -S 0.0.0.0:8888 -t public public/index.php
```

Go to https://localhost:8888/ping and you should get a json response with the current datetime.
