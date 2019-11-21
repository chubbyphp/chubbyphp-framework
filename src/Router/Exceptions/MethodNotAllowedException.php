<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Exceptions;

use Chubbyphp\Framework\Router\RouterException;

final class MethodNotAllowedException extends RouterException implements RouterExceptionInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $title;

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
        ), 405);
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
