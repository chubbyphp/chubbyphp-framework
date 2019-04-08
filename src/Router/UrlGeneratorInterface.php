<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;

interface UrlGeneratorInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param string                 $name
     * @param array                  $parameters
     *
     * @return string
     *
     * @throws UrlGeneratorException
     */
    public function generateUri(ServerRequestInterface $request, string $name, array $parameters = []): string;

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return string
     *
     * @throws UrlGeneratorException
     */
    public function generatePath(string $name, array $parameters = []): string;
}
