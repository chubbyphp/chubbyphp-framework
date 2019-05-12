# Add controller service provider

## Create app/ServiceProvider/ControllerServiceProvider.php

We're creating the ControllerServiceProvider.php which contains the controller service defintions.

```php
<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\Controller\PingController;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class ControllerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container[PingController::class] = function () use ($container) {
            return new PingController($container[ResponseFactoryInterface::class]);
        };
    }
}
```

## Register the ControllerServiceProvider to app/web.php.

We're registering the created ControllerServiceProvider within web.php.
The reason why we took this web.php instead of the container.php is, cause its only web app related.

```php
$container->register(new ControllerServiceProvider());
```

## Replace Route within app/web.php

We're replacing the not lazy route definition which a lazy ones using the PingController service defintion.
The LazyRequestHandler depends on a Psr11 Container implementation, thats the reason for use this adapter.

```php
$route = Route::get('/ping', 'ping', new PingController($responseFactory));
```

with:

```php
$psrContainer = new \Pimple\Psr11\Container($container);

$route = Route::get('/ping', 'ping', new LazyRequestHandler($psrContainer, PingController::class));
```

## Test the application

We're testing the current state.

```bash
cd /path/to/my/project
php -S 0.0.0.0:8888 -t public public/index.php
```

Go to https://localhost:8888/ping and you should get a json response with the current datetime.
