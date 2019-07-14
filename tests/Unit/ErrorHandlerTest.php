<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\ResponseHandler;

use Chubbyphp\Framework\ErrorHandler;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\ErrorHandler
 *
 * @internal
 */
final class ErrorHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $file = __FILE__;
        $line = __LINE__;

        try {
            ErrorHandler::handle(E_USER_WARNING, 'test', $file, $line);

            self::fail('Should throw a ErrorException');
        } catch (\ErrorException $e) {
            self::assertSame('test', $e->getMessage());
            self::assertSame(0, $e->getCode());
            self::assertSame(E_USER_WARNING, $e->getSeverity());
            self::assertSame($file, $e->getFile());
            self::assertSame($line, $e->getLine());

            return;
        }
    }

    public function testHandleWithZeroErrorReporting(): void
    {
        // this happens if you register the error handler and and @ is added to a function all
        $errorReporting = error_reporting(0);

        $return = ErrorHandler::handle(E_USER_WARNING, 'test', __FILE__, __LINE__);

        error_reporting($errorReporting);

        self::assertFalse($return);
    }

    public function testWithRegistredErrorHandler(): void
    {
        error_clear_last();

        set_error_handler([ErrorHandler::class, 'handle']);

        try {
            trigger_error('test', E_USER_WARNING);

            restore_error_handler();

            self::fail('Should throw a ErrorException');
        } catch (\ErrorException $e) {
            restore_error_handler();

            self::assertSame('test', $e->getMessage());
            self::assertSame(0, $e->getCode());
            self::assertSame(E_USER_WARNING, $e->getSeverity());

            $error = error_get_last();

            error_clear_last();

            self::assertNull($error);
        }
    }

    public function testWithRegistredErrorHandlerAndZeroErrorReporting(): void
    {
        error_clear_last();

        set_error_handler([ErrorHandler::class, 'handle']);

        @trigger_error('test', E_USER_WARNING);

        restore_error_handler();

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame('test', $error['message']);
        self::assertSame(E_USER_WARNING, $error['type']);
    }

    public function testWithRegistredErrorHandlerAndHigherErrorReporting(): void
    {
        $errorReporting = error_reporting(E_USER_NOTICE);

        error_clear_last();

        set_error_handler([ErrorHandler::class, 'handle']);

        trigger_error('test', E_USER_WARNING);

        error_reporting($errorReporting);

        restore_error_handler();

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame('test', $error['message']);
        self::assertSame(E_USER_WARNING, $error['type']);
    }

    public function testWithRegistredErrorHandlerAndLowerErrorReporting(): void
    {
        $errorReporting = error_reporting(E_USER_WARNING);

        error_clear_last();

        set_error_handler([ErrorHandler::class, 'handle']);

        try {
            trigger_error('test', E_USER_WARNING);

            error_reporting($errorReporting);

            restore_error_handler();

            self::fail('Should throw a ErrorException');
        } catch (\ErrorException $e) {
            error_reporting($errorReporting);

            restore_error_handler();

            self::assertSame('test', $e->getMessage());
            self::assertSame(0, $e->getCode());
            self::assertSame(E_USER_WARNING, $e->getSeverity());

            $error = error_get_last();

            error_clear_last();

            self::assertNull($error);
        }
    }
}
