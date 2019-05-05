# Add dependency injection container

## Composer

```bash
cd /path/to/my/project
composer require pimple/pimple "^3.2.3"
```

## Create app/container.php

```php
<?php

declare(strict_types=1);

namespace App;

use Pimple\Container;

$container = new Container();

return $container;
```

## Add app/container.php to app/app.php

Replace the following line with:

```php
require __DIR__.'/bootstrap.php';
```

```php
require __DIR__.'/bootstrap.php';

$container = require __DIR__.'/container.php';
```

## Test the application

```bash
cd /path/to/my/project
php -S 0.0.0.0:8888 -t public public/index.php
```

Go to https://localhost:8888/ping and you should get a json response with the current datetime.
