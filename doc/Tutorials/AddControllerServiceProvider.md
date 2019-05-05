# Add controller service provider

## Create app/ServiceProvider/ControllerServiceProvider.php

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

## Register the ControllerServiceProvider to app/app.php (cause its only web related)

```php
$container->register(new ControllerServiceProvider());
```

## Replace Route within app/app.php

```php
$route = Route::get('/ping', 'ping', new PingController($responseFactory));
```

```php
$route = Route::get('/ping', 'ping', $container[PingController::class]);
```

## Test the application

```bash
cd /path/to/my/project
php -S 0.0.0.0:8888 -t public public/index.php
```

Go to https://localhost:8888/ping and you should get a json response with the current datetime.
