# GroupCollector

Extends [AbstractCollector](AbstractCollector.md), see there for the inherited `route` / `delete` / `get` / `head` / `options` / `patch` / `post` / `put` / `group` methods.

## Methods

### create

```php
<?php

use Chubbyphp\Framework\Facade\GroupCollector;

$groupCollector = GroupCollector::create();
```

### getChildren

```php
<?php

use Chubbyphp\Framework\Facade\GroupCollector;
use Chubbyphp\Framework\Router\GroupInterface;
use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Some\Psr7\Response;

$handler = new class() implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
};

$groupCollector = GroupCollector::create()
    ->get('/{id}', 'read', $handler)
    ->group('/sub', static fn (GroupCollector $sub) => $sub->get('', 'sub_read', $handler));

/** @var list<GroupInterface|RouteInterface> $children */
$children = $groupCollector->getChildren();
```
