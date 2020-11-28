<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

/**
 * @deprecated
 */
final class ErrorHandler
{
    /**
     * @var mixed
     */
    private $errorHandler;

    public function __construct()
    {
        $this->errorHandler = set_error_handler(null);

        restore_error_handler();
    }

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

    public function errorToException(
        int $severity,
        string $message,
        string $file = __FILE__,
        int $line = __LINE__
    ): bool {
        if (is_callable($this->errorHandler)) {
            @($this->errorHandler)($severity, $message, $file, $line);
        }

        return self::handle($severity, $message, $file, $line);
    }
}
