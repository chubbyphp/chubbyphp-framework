# Move PingController into a class

## Create app/Controller directory

We're creating and Controller directory where all controllers will take place in.

```bash
cd /path/to/my/project
mkdir app/Controller
```

## Create app/Controller/PingController.php

We're creating the PingController.php which implements the PSR15's RequestHandlerInterface.

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PingController implements RequestHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Expires', '0');

        $response->getBody()->write(json_encode(['datetime' => date(\DateTime::ATOM)]));

        return $response;
    }
}
```

## Replace Route within app/app.php

We're replacing the exising $route definition with the app.php whith one using the PingController.php.
This is only a temporary replacement, cause we lost the lazyness of the callable.

```php
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
```

```php
$route = Route::get('/ping', 'ping', new PingController($responseFactory));
```

## Test the application

We're testing the current state.

```bash
cd /path/to/my/project
php -S 0.0.0.0:8888 -t public public/index.php
```

Go to https://localhost:8888/ping and you should get a json response with the current datetime.
