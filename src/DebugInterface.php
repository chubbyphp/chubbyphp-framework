<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

interface DebugInterface
{
    /**
     * @return array<string, mixed>
     */
    public function debug(): array;
}
