<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\AuraRouter;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\Generator;
use Aura\Router\RouterContainer;
use Chubbyphp\Framework\Router\UrlGeneratorException;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chubbyphp\Framework\Router\GroupInterface;

final class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @param GroupInterface $group
     */
    public function __construct(GroupInterface $group)
    {
        $this->routes = $group->getRoutes();

        $routerContainer = new RouterContainer();
        $map = $routerContainer->getMap();

        foreach ($this->routes as $route) {
            $options = $route->getPathOptions();

            $auraRoute = $map->route($route->getName(), $route->getPath());
            $auraRoute->allows($route->getMethod());
            $auraRoute->tokens($options['tokens'] ?? []);
            $auraRoute->defaults($options['defaults'] ?? []);
        }

        $this->generator = $routerContainer->getGenerator();
    }

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
    ): string {
        $uri = $request->getUri();
        $requestTarget = $this->generatePath($name, $attributes, $queryParams);

        return $uri->getScheme().'://'.$uri->getAuthority().$requestTarget;
    }

    /**
     * @param string   $name
     * @param string[] $attributes
     * @param array    $queryParams
     *
     * @return string
     *
     * @throws UrlGeneratorException
     */
    public function generatePath(string $name, array $attributes = [], array $queryParams = []): string
    {
        try {
            $path = $this->generator->generate($name, $attributes);

            if ([] === $queryParams) {
                return $path;
            }

            return $path.'?'.http_build_query($queryParams);
        } catch (RouteNotFound $exception) {
            throw UrlGeneratorException::createForMissingRoute($name);
        }
    }
}
