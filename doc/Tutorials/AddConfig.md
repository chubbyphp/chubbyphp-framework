# Add config

## Composer

We're installing chubbyphp/chubbyphp-config a minimal configuration library with environment support.

```bash
cd /path/to/my/project
composer require chubbyphp/chubbyphp-config "^1.1.2"
```

## Add var/cache to .gitignore

We're adding the var/cache directory to the .gitignore.

```
cd /path/to/my/project
printf "var/cache\n" >> .gitignore
```

## Create a app/Config

We're creating and Config directory where all configurations will take place in.

```bash
cd /path/to/my/project
mkdir app/Config
```

## Create app/Config/AbstractConfig.php

We're creating the AbstractConfig.php which contains the basic implementation of the config interface.

```php
<?php

declare(strict_types=1);

namespace App\Config;

use Chubbyphp\Config\ConfigInterface;

abstract class AbstractConfig implements ConfigInterface
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @param string $rootDir
     *
     * @return self
     */
    public static function create(string $rootDir): ConfigInterface
    {
        $config = new static();
        $config->rootDir = $rootDir;

        return $config;
    }

    private function __construct()
    {
    }

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        return [
            'cache' => $this->getCacheDir(),
        ];
    }

    /**
     * @return string
     */
    abstract protected function getEnv(): string;

    /**
     * @return string
     */
    protected function getCacheDir(): string
    {
        return $this->rootDir.'/var/cache/'.$this->getEnv();
    }
}
```

## Create app/Config/ProdConfig.php

We're creating the ProdConfig.php which contains the production configuration based on the AbstractConfig.php

```php
<?php

declare(strict_types=1);

namespace App\Config;

class ProdConfig extends AbstractConfig
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'debug' => false,
            'routerCacheFile' => $this->getCacheDir() . '/fast-route-routes.php',
        ];
    }

    /**
     * @return string
     */
    protected function getEnv(): string
    {
        return 'prod';
    }
}
```

## Create app/Config/DevConfig.php

We're creating the DevConfig.php which contains the development configuration based on the ProdConfig.php

```php
<?php

declare(strict_types=1);

namespace App\Config;

class DevConfig extends ProdConfig
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        $config = parent::getConfig();
        $config['debug'] = true;
        $config['routerCacheFile'] = null;

        return $config;
    }

    /**
     * @return string
     */
    protected function getEnv(): string
    {
        return 'dev';
    }
}
```

## Add ConfigServiceProvider to app/container.php

We're adding the ConfigServiceProvider to the container.php. It's important to keep it
the last service provider, who beeing able to override all others.

```php
<?php

declare(strict_types=1);

namespace App;

use App\Config\DevConfig;
use App\Config\ProdConfig;
use Chubbyphp\Config\ConfigMapping;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\Pimple\ConfigServiceProvider;
use Pimple\Container;

$configProvider = new ConfigProvider(__DIR__.'/../', [
    new ConfigMapping('dev', DevConfig::class),
    new ConfigMapping('prod', ProdConfig::class),
]);

$container = new Container(['env' => $env]);

// always load this service provider last
// so that the values of other service providers can be overwritten.
$container->register(new ConfigServiceProvider($configProvider));

return $container;
```

## Create public/index_prod.php

We're creating the index.php which is the production frontcontroller of the web application.

```php
<?php

declare(strict_types=1);

use Zend\Diactoros\ServerRequestFactory;

$env = 'prod';

$app = require __DIR__.'/../app/app.php';

$app->send($app->handle(ServerRequestFactory::fromGlobals()));
```

## Create public/index_dev.php

We're creating the index.php which is the development frontcontroller of the web application.

```php
<?php

declare(strict_types=1);

use Zend\Diactoros\ServerRequestFactory;

$env = 'dev';

$app = require __DIR__.'/../app/app.php';

$app->send($app->handle(ServerRequestFactory::fromGlobals()));
```

## Replace public/index.php with a symlink to public/index_dev.php

We're replacing the exiting index.php with a symlink to the index_dev.php, which contains the $env variable.

```bash
cd /path/to/my/project/public
ln -sf index_dev.php index.php
```

## Use config value in app/app.php

We're start to use the configuration by using the `routerCacheFile` and the `debug` config key.

Replace the following line with:

```php
$app = new Application(
    new FastRouteRouter([$route]),
    new MiddlewareDispatcher(),
    new ExceptionHandler($responseFactory, true)
);
```

with:

```php
$app = new Application(
    new FastRouteRouter([$route], $container['routerCacheFile']),
    new MiddlewareDispatcher(),
    new ExceptionHandler($responseFactory, $container['debug'])
);
```

## Test the application

We're testing the current state.

```bash
cd /path/to/my/project
php -S 0.0.0.0:8888 -t public public/index.php
```

Go to https://localhost:8888/ping and you should get a json response with the current datetime.
