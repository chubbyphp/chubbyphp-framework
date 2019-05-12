# Initialize

## Create .gitignore

We're adding the vendor directory to the .gitignore.

```
cd /path/to/my/project
printf "vendor\n" > .gitignore
```

## Composer

We're installing chubbyphp-framework with fast-route and zend-diactoros.

```bash
cd /path/to/my/project
composer require chubbyphp/chubbyphp-framework "^1.0@beta" \
    nikic/fast-route "^1.3" zendframework/zend-diactoros "^2.1.2"
```

## Create app directory

We're creating and app directory where the whole application will take place in.

```bash
cd /path/to/my/project
mkdir app
```

## Add autoload section to composer.json

We're creating the app directory and add it to the autoload section of composer with the App namespace.

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

We're creating the bootstrap.php, which adds the autoloader and registers the error handler.

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\ErrorHandler;

$loader = require __DIR__.'/../vendor/autoload.php';

set_error_handler([ErrorHandler::class, 'handle']);
```

## Create app/web.php

We're creating the web.php which contains the whole (web) application.

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

$web = new Application(
    new FastRouteRouter([$route]),
    new MiddlewareDispatcher(),
    new ExceptionHandler($responseFactory, true)
);

return $web;
```

## Create public directory

We're creating the public directory where all public files take place in.

```bash
cd /path/to/my/project
mkdir public
```

## Create public/index.php

We're creating the index.php which is the frontcontroller of the web application.

```php
<?php

declare(strict_types=1);

use Zend\Diactoros\ServerRequestFactory;

$web = require __DIR__.'/../app/web.php';

$web->send($web->handle(ServerRequestFactory::fromGlobals()));
```

## Test the application

We're testing the current state.

```bash
cd /path/to/my/project
php -S 0.0.0.0:8888 -t public public/index.php
```

Go to https://localhost:8888/ping and you should get a json response with the current datetime.
