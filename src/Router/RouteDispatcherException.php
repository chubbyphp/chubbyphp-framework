<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

final class RouteDispatcherException extends \RuntimeException
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $title;

    /**
     * @param mixed ...$args
     */
    private function __construct(...$args)
    {
        parent::__construct(...$args);
    }

    /**
     * @param string $path
     *
     * @return self
     */
    public static function createForNotFound(string $path): self
    {
        $self = new self(sprintf(
            'The page "%s" you are looking for could not be found.'
                .' Check the address bar to ensure your URL is spelled correctly.',
            $path
        ), 404);
        $self->type = 'https://tools.ietf.org/html/rfc7231#section-6.5.4';
        $self->title = 'Page not found';

        return $self;
    }

    /**
     * @param string $method
     * @param array  $methods
     * @param string $path
     *
     * @return self
     */
    public static function createForMethodNotAllowed(string $method, array $methods, string $path): self
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

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
