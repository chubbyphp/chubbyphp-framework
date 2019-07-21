<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

final class ExceptionHelper
{
    /**
     * @param \Throwable $exception
     *
     * @return array<int, array<string, mixed>>
     */
    public static function toArray(\Throwable $exception): array
    {
        $exceptions = [];
        do {
            $exceptions[] = [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        } while ($exception = $exception->getPrevious());

        return $exceptions;
    }
}
