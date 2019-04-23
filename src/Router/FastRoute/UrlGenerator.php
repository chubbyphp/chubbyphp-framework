<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\FastRoute;

use Chubbyphp\Framework\Router\RouteCollectionInterface;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\UrlGeneratorException;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use FastRoute\RouteParser\Std as RouteParser;
use Psr\Http\Message\ServerRequestInterface;

final class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var RouteInterface[]
     */
    private $routes;

    /**
     * @var RouteParser
     */
    private $routeParser;

    /**
     * @param RouteCollectionInterface $routeCollection
     */
    public function __construct(RouteCollectionInterface $routeCollection)
    {
        $this->routes = $routeCollection->getRoutes();
        $this->routeParser = new RouteParser();
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
        $route = $this->getRoute($name);

        $routePartSets = array_reverse($this->routeParser->parse($route->getPath()));

        $routeIndex = $this->getRouteIndex($routePartSets, $attributes);

        $pathParts = [];
        foreach ($routePartSets[$routeIndex] as $routePart) {
            if (is_array($routePart)) {
                $pathParts[] = $attributes[$routePart[0]] ?? '{'.$routePart[0].'}';
            } else {
                $pathParts[] = $routePart;
            }
        }

        $path = implode('', $pathParts);

        if ([] === $queryParams) {
            return $path;
        }

        return $path.'?'.http_build_query($queryParams);
    }

    /**
     * @param string $name
     *
     * @return RouteInterface
     *
     * @throws UrlGeneratorException
     */
    private function getRoute(string $name): RouteInterface
    {
        if (!isset($this->routes[$name])) {
            throw UrlGeneratorException::createForMissingRoute($name);
        }

        return $this->routes[$name];
    }

    /**
     * @param array $routePartSets
     * @param array $attributes
     *
     * @return int
     */
    private function getRouteIndex(array $routePartSets, array $attributes): int
    {
        foreach ($routePartSets as $routeIndex => $routeParts) {
            $missingParameters = [];
            foreach ($routeParts as $routePart) {
                if (is_array($routePart)) {
                    $parameter = $routePart[0];
                    if (!isset($attributes[$parameter])) {
                        $missingParameters[] = $parameter;

                        break;
                    }
                }
            }

            if ([] === $missingParameters) {
                return $routeIndex;
            }
        }

        return $routeIndex;
    }
}
