# UrlMatcherMiddleware

## Methods

### process

```php
<?php

use Chubbyphp\Framework\Middleware\UrlMatcherMiddleware;
use Chubbyphp\Framework\Router\Some\UrlMatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\ServerRequest;
use Some\Psr7\ResponseFactory;

$request = new ServerRequest();

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

$urlMatcher = new UrlMatcher();
$responseFactory = new ResponseFactory();

$urlMatcherMiddleware = new UrlMatcherMiddleware($urlMatcher, $responseFactory);

$response = $urlMatcherMiddleware->process($request, $handler);
```
