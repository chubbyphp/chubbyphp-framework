# Roadrunner

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Spiral\Goridge\StreamRelay;
use Spiral\RoadRunner\Worker;
use Spiral\RoadRunner\PSR7Client;

ini_set('display_errors', 'stderr');

$app = new Application();

$worker = new Worker(new StreamRelay(STDIN, STDOUT));
$psr7 = new PSR7Client($worker);

while ($req = $psr7->acceptRequest()) {
    try {
        $psr7->respond($app->handle($req));
    } catch (\Throwable $e) {
        $psr7->getWorker()->error((string)$e);
    }
}
```
