# UrlGeneratorMiddleware

Adds the given `UrlGeneratorInterface` as request attribute `urlGenerator` (`UrlGeneratorMiddleware::ATTRIBUTE`),
so it is available within the following middlewares and request handlers.

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\UrlGeneratorMiddleware;
use Chubbyphp\Framework\Router\Some\UrlGenerator;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\ServerRequest;

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

$urlGeneratorMiddleware = new UrlGeneratorMiddleware(new UrlGenerator());

$response = $urlGeneratorMiddleware->process($request, $handler);
```
