<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Exceptions;

use Fig\Http\Message\StatusCodeInterface as StatusCode;

final class MethodNotAllowedException extends RouterException implements HttpExceptionInterface
{
    private string $type;

    private string $title;

    private function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param array<string> $methods
     */
    public static function create(string $path, string $method, array $methods): self
    {
        $self = new self(sprintf(
            'Method "%s" at path "%s" is not allowed. Must be one of: "%s"',
            $method,
            $path,
            implode('", "', $methods)
        ), StatusCode::STATUS_METHOD_NOT_ALLOWED);
        $self->type = 'https://tools.ietf.org/html/rfc7231#section-6.5.5';
        $self->title = 'Method not allowed';

        return $self;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
