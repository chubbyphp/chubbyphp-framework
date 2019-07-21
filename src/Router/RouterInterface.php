<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function match(ServerRequestInterface $request): RouteInterface;

    /**
     * @param ServerRequestInterface $request
     * @param string                 $name
     * @param array<string, string>  $attributes
     * @param array<string, mixed>   $queryParams
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
     * @param string                $name
     * @param array<string, string> $attributes
     * @param array<string, mixed>  $queryParams
     *
     * @throws RouterException
     *
     * @return string
     */
    public function generatePath(string $name, array $attributes = [], array $queryParams = []): string;
}
