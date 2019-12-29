<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Exceptions;

interface RouterExceptionInterface
{
    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return int
     */
    public function getCode();

    public function getType(): string;

    public function getTitle(): string;
}
