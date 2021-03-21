# ReactPHP

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use React\EventLoop\Factory;
use React\Http\Server;
use React\Socket\Server as Socket;

$app = new Application();

$loop = Factory::create();

$socket = new Socket(8080, $loop);

$server = new Server($app);
$server->listen($socket);

$loop->run();
```
