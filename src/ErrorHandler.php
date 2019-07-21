<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

final class ErrorHandler
{
    public static function handle(
        int $severity,
        string $message,
        string $file = __FILE__,
        int $line = __LINE__
    ): bool {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
}
