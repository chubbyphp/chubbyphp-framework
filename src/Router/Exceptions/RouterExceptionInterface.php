<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Exceptions;

interface RouterExceptionInterface
{
    public function getMessage();

    public function getCode();

    public function getType(): string;

    public function getTitle(): string;
}
