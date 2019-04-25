# Aura.Router

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandler;
use Chubbyphp\Framework\Router\AuraRouter;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

$responseFactory = new ResponseFactory();

$route = Route::get('/hello/{name}', 'hello',
    new class($responseFactory) implements RequestHandlerInterface
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

        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            $name = $request->getAttribute('name');
            $response = $this->responseFactory->createResponse();
            $response->getBody()->write(sprintf('Hello, %s', $name));

            return $response;
        }
    }
)->pathOptions(['tokens' => ['name' => '[a-z]+']]);

$app = new Application(
    new AuraRouter([$route]),
    new MiddlewareDispatcher(),
    new ExceptionResponseHandler($responseFactory)
);

$app->send($app->handle(ServerRequestFactory::fromGlobals()));
```
