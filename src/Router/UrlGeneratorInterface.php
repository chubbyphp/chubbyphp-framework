<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

interface UrlGeneratorInterface
{
    public function requestTarget(string $name, array $arguments): string;
}
