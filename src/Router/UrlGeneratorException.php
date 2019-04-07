<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

final class UrlGeneratorException extends \RuntimeException
{
    /**
     * @param mixed ...$args
     */
    private function __construct(...$args)
    {
        parent::__construct(...$args);
    }

    /**
     * @param string[] $missingParameters
     *
     * @return self
     */
    public static function createForMissingParameters(array $missingParameters): self
    {
        return new self(sprintf('Missing parameters: "%s"', implode('", "', $missingParameters)), 1);
    }

    /**
     * @param InvalidParameter[] $invalidParameters
     *
     * @return self
     */
    public static function createForInvalidParameters(array $invalidParameters): self
    {
        $message = '';
        foreach ($invalidParameters as $invalidParameter) {
            /* @var InvalidParameter $invalidParameter */
            $message .= sprintf(
                'Parameter "%s" with value "%s" does not match "%s"'.PHP_EOL,
                $invalidParameter->getParameter(),
                $invalidParameter->getValue(),
                $invalidParameter->getPattern()
            );
        }

        return new self(trim($message), 2);
    }
}
