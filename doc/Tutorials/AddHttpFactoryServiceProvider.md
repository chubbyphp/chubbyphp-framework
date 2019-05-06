# Add http factory service provider

## Create a app/ServiceProvider

We're creating and ServiceProvider directory where all service providers will take place in.

```bash
cd /path/to/my/project
mkdir app/ServiceProvider
```

## Create app/ServiceProvider/HttpFactoryServiceProvider.php

We're creating the HttpFactoryServiceProvider.php which contains a PSR17 implementation of a response factory.

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

## Register the FactoryServiceProvider to app/app.php

We're registering the created HttpFactoryServiceProvider within app.php.
The reason why we took this app.php instead of the container.php is, cause its only web app related.

```php
$container->register(new HttpFactoryServiceProvider());
```

## Replace Route within app/app.php

We're replacing the ResponseFactory within the app.php with the service defintion.

```php
$responseFactory = new ResponseFactory();
```

with:

```php
$responseFactory = $container[ResponseFactoryInterface::class];
```

## Test the application

We're testing the current state.

```bash
cd /path/to/my/project
php -S 0.0.0.0:8888 -t public public/index.php
```

Go to https://localhost:8888/ping and you should get a json response with the current datetime.
