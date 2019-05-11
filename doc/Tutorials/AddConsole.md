# Add console

## Composer

We're installing symfony/console.

```bash
cd /path/to/my/project
composer require symfony/console "^4.2.8"
```

## Create app/console.php

We're creating the console.php to have a console entry point.

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App;

use App\ServiceProvider\ConsoleServiceProvider;
use Pimple\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;

require __DIR__.'/bootstrap.php';

$input = new ArgvInput();

$env = $input->getParameterOption(['--env', '-e'], 'dev');

/** @var Container $container */
$container = require __DIR__.'/container.php';
$container->register(new ConsoleServiceProvider());

$console = new Application();
$console->getDefinition()->addOption(
    new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev')
);
$console->addCommands($container['console.commands']);
$console->run($input);
```

## Create app/ServiceProvider/ConsoleServiceProvider.php

We're creating the ConsoleServiceProvider.php which contains the CleanDirectoriesCommand command.

```php
<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use Chubbyphp\Config\Command\CleanDirectoriesCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ConsoleServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['console.commands'] = function () use ($container) {
            return [
                new CleanDirectoriesCommand($container['chubbyphp.config.directories']),
            ];
        };
    }
}
```

## Create bin directory.

We're creating the bin directory where we will put the console.

```bash
cd /path/to/my/project
mkdir bin
```

## Create a symlink to app/console.php

We're creating a symlink from bin/console to app/console.php and make it executable.

```bash
cd /path/to/my/project/bin
ln -s ../app/console.php console
chmod +x console
```

## Test the console

We're testing the current state.

```bash
cd /path/to/my/project
bin/console config:clean-directories cache
```
