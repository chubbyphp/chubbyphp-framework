# UrlGeneratorMiddleware

Adds the given `UrlGeneratorInterface` as request attribute `urlGenerator` (`UrlGeneratorMiddleware::ATTRIBUTE`),
so it is available within the following middlewares and request handlers.

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\UrlGeneratorMiddleware;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RoutesByName;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;
use Some\Psr7\ServerRequest;
use Some\Router\UrlGenerator;

$request = new ServerRequest();

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $request->getAttribute(UrlGeneratorMiddleware::ATTRIBUTE);

        $urlGenerator->generatePath('pet_read', ['id' => '1']);

        return new Response();
    }
};

$urlGenerator = new UrlGenerator(new RoutesByName([
    Route::get('/pets/{id}', 'pet_read', $handler),
]));

$urlGeneratorMiddleware = new UrlGeneratorMiddleware($urlGenerator);

$response = $urlGeneratorMiddleware->process($request, $handler);
```
