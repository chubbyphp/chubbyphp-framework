# Aura.Router

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandler;
use Chubbyphp\Framework\Router\AuraRouter;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

$responseFactory = new ResponseFactory();

$route = Route::get('/hello/{name}', 'hello', new CallbackRequestHandler(
    function (ServerRequestInterface $request) use ($responseFactory) {
        $name = $request->getAttribute('name');
        $response = $responseFactory->createResponse();
        $response->getBody()->write(sprintf('Hello, %s', $name));

        return $response;
    }
))->pathOptions(['tokens' => ['name' => '[a-z]+']]);

$app = new Application(
    new AuraRouter([$route]),
    new MiddlewareDispatcher(),
    new ExceptionResponseHandler($responseFactory)
);

$app->send($app->handle(ServerRequestFactory::fromGlobals()));
```

## Path Options

Supported options:

 * [defaults][10]
 * [host][11]
 * [secure][12]
 * [special][13]
 * [tokens][10]
 * [wildcard][14]

Not supported options:

 * [accepts][20] => 406
 * [allows][21] => handled by abstraction
 * [auth][22] => 401
 * [extras][23] => hidden by abstraction

[10]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#placeholder-tokens-and-default-values
[11]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#host-matching
[12]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#secure-protocols
[13]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#route-specific-matching-logic
[14]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#wildcard-attributes

[20]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#accept-headers
[21]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#multiple-http-verbs
[22]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#authentication
[23]: https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md#custom-extras
