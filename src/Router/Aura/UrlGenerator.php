<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Aura;

use Aura\Router\Generator;
use Aura\Router\RouterContainer;
use Chubbyphp\Framework\Router\RouteCollectionInterface;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Psr\Http\Message\ServerRequestInterface;
use Aura\Router\Exception\RouteNotFound;
use Chubbyphp\Framework\Router\UrlGeneratorException;

final class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @param RouteCollectionInterface $routeCollection
     */
    public function __construct(RouteCollectionInterface $routeCollection)
    {
        $this->routes = $routeCollection->getRoutes();

        $routerContainer = new RouterContainer();
        $map = $routerContainer->getMap();

        foreach ($this->routes as $route) {
            $options = $route->getOptions();

            $auraRoute = $map->route($route->getName(), $route->getPattern());
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
