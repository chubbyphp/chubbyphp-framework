# Roadrunner

## .rr.yaml

```yaml
server:
  command: "php worker.php"

http:
  address: 0.0.0.0:8080
  pool:
    num_workers: 4
```

## worker.php

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UploadedFileFactory;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;

ini_set('display_errors', 'stderr');

$app = new Application([]);

$worker = new PSR7Worker(
    Worker::create(),
    new ServerRequestFactory(),
    new StreamFactory(),
    new UploadedFileFactory()
);

while ($req = $worker->waitRequest()) {
    try {
        $worker->respond($app->handle($req));
    } catch (\Throwable $e) {
        $worker->getWorker()->error((string)$e);
    }
}
```
