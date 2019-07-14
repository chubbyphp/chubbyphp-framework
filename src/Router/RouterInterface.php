<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return RouteInterface
     */
    public function match(ServerRequestInterface $request): RouteInterface;

    /**
     * @param ServerRequestInterface $request
     * @param string                 $name
     * @param string[]               $attributes
     * @param array                  $queryParams
     *
     * @throws RouterException
     *
     * @return string
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
     * @throws RouterException
     *
     * @return string
     */
    public function generatePath(string $name, array $attributes = [], array $queryParams = []): string;
}
