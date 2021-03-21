# Emitter

## Methods

### emit

```php
<?php

use Chubbyphp\Framework\Emitter\Emitter;
use Some\Psr7\Response;

$response = new Response();

$emitter = new Emitter();
$emitter->emit($response);
```
