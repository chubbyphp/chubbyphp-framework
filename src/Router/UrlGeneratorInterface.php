<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;

interface UrlGeneratorInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param string                 $name
     * @param string[]               $attributes
     * @param array                  $queryParams
     *
     * @return string
     *
     * @throws UrlGeneratorException
     */
    public function generateUrl(
        ServerRequestInterface $request,
        string $name,
        array $attributes = [],
        array $queryParams = []
    ): string;

    /**
     * @param string   $name
     * @param string[] $attributes
     * @param array    $queryParams
     *
     * @return string
     *
     * @throws UrlGeneratorException
     */
    public function generatePath(string $name, array $attributes = [], array $queryParams = []): string;
}
