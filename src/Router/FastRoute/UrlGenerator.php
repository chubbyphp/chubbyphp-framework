<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\FastRoute;

use Chubbyphp\Framework\Router\RouteCollectionInterface;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\UrlGeneratorException;
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
     *
     * @throws UrlGeneratorException
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
            throw UrlGeneratorException::createForMissingParameter();
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

            if (null === $parameter = $this->getParameter($routePart, $parameters)) {
                return null;
            }

            $usedParameters[] = $parameter;
            $requestTargetSegments[] = $parameters[$parameter];
        }

        foreach ($usedParameters as $usedParameter) {
            unset($parameters[$usedParameter]);
        }

        return implode('', $requestTargetSegments);
    }

    /**
     * @param array $routePart
     * @param array $parameters
     *
     * @return string|null
     *
     * @throws \InvalidArgumentException
     */
    private function getParameter(array $routePart, array $parameters): ?string
    {
        $parameter = $routePart[0];

        if (!isset($parameters[$parameter])) {
            return null;
        }

        $pattern = '/^'.str_replace('/', '\/', $routePart[1]).'$/';
        $value = (string) $parameters[$parameter];

        if (1 !== preg_match($pattern, $value)) {
            throw UrlGeneratorException::createForInvalidParameter($parameter, $value, $pattern);
        }

        return $parameter;
    }
}
