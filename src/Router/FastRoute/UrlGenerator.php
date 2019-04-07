<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\FastRoute;

use Chubbyphp\Framework\Router\RouteCollectionInterface;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use FastRoute\RouteParser;
use FastRoute\RouteParser\Std;

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
     * @param RouteCollectionInterface $data
     */
    public function __construct(RouteCollectionInterface $routeCollection, RouteParser $routeParser = null)
    {
        $this->routes = $routeCollection->getRoutes();
        $this->routeParser = $routeParser ?? new Std();
    }

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return string
     */
    public function requestTarget(string $name, array $parameters): string
    {
        $route = $this->getRoute($name);

        $path = null;
        foreach (array_reverse($this->routeParser->parse($route->getPath())) as $routeParts) {
            if (null !== $path = $this->getPath($routeParts, $parameters)) {
                break;
            }
        }

        if (null === $path) {
            throw new \InvalidArgumentException('Missing arguments to build the requestTarget');
        }

        if ([] === $parameters) {
            return $path;
        }

        return $path.'?'.http_build_query($parameters);
    }

    /**
     * @param string $name
     *
     * @return RouteInterface
     *
     * @throws \InvalidArgumentException
     */
    private function getRoute(string $name): RouteInterface
    {
        if (!isset($this->routes[$name])) {
            throw new \InvalidArgumentException(sprintf('There is no route with name: %s', $name));
        }

        return $this->routes[$name];
    }

    /**
     * @param array $routeParts
     * @param array $parameters
     *
     * @return string|null
     */
    private function getPath(array $routeParts, array &$parameters): ?string
    {
        $usedParameters = [];
        $requestTargetSegments = [];

        foreach ($routeParts as $routePart) {
            if (is_string($routePart)) {
                $requestTargetSegments[] = $routePart;

                continue;
            }

            $parameter = $routePart[0];

            if (!isset($parameters[$parameter])) {
                return null;
            }

            $pattern = '/^'.str_replace('/', '\/', $routePart[1]).'$/';
            $value = (string) $parameters[$parameter];

            if (1 !== preg_match($pattern, $value)) {
                return null;
            }

            $usedParameters[] = $parameter;
            $requestTargetSegments[] = $value;
        }

        foreach ($usedParameters as $usedParameter) {
            unset($parameters[$usedParameter]);
        }

        return implode('', $requestTargetSegments);
    }
}
