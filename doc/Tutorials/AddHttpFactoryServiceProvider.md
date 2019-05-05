# Add http factory service provider

## Create a app/ServiceProvider

```bash
cd /path/to/my/project
mkdir app/ServiceProvider
```

## Create app/ServiceProvider/HttpFactoryServiceProvider.php

```php
<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Zend\Diactoros\ResponseFactory;

final class HttpFactoryServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container[ResponseFactoryInterface::class] = function () {
            return new ResponseFactory();
        };
    }
}
```

## Register the FactoryServiceProvider to app/app.php (cause its only web related)

```php
$container->register(new HttpFactoryServiceProvider());
```

## Replace Route within app/app.php

```php
$responseFactory = new ResponseFactory();
```

```php
$responseFactory = $container[ResponseFactoryInterface::class];
```

## Test the application

```bash
cd /path/to/my/project
php -S 0.0.0.0:8888 -t public public/index.php
```

Go to https://localhost:8888/ping and you should get a json response with the current datetime.
