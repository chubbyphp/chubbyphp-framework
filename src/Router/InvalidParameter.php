<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

final class InvalidParameter
{
    /**
     * @var string
     */
    private $parameter;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @param string $parameter
     * @param string $value
     * @param string $pattern
     */
    public function __construct(string $parameter, string $value, string $pattern)
    {
        $this->parameter = $parameter;
        $this->value = $value;
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getParameter(): string
    {
        return $this->parameter;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }
}
