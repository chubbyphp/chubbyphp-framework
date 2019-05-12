# Add dependency injection container

## Composer

We're installing pimple/pimple a minimal dependency injection container using callables.

```bash
cd /path/to/my/project
composer require pimple/pimple "^3.2.3"
```

## Create app/container.php

We're creating the container.php which contains the dependency injection container.

```php
<?php

declare(strict_types=1);

namespace App;

use Pimple\Container;

$container = new Container();

return $container;
```

## Add app/container.php to app/web.php

We're adding the container to the web.php by replacing the following line:

```php
require __DIR__.'/bootstrap.php';
```

with:

```php
require __DIR__.'/bootstrap.php';

$container = require __DIR__.'/container.php';
```

## Test the application

We're testing the current state.

```bash
cd /path/to/my/project
php -S 0.0.0.0:8888 -t public public/index.php
```

Go to https://localhost:8888/ping and you should get a json response with the current datetime.
