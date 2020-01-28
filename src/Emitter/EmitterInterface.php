<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Emitter;

use Psr\Http\Message\ResponseInterface;

interface EmitterInterface
{
    public function emit(ResponseInterface $response): void;
}
