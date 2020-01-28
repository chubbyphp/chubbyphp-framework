# Emitter

## Methods

### emit

```php
<?php

use Chubbyphp\Framework\Emitter\Emitter;
use Psr\Http\Message\ResponseInterface;

/** @var ResponseInterface $response */
$response = ...;

$emitter = new Emitter();
$emitter->emit($response);
```
