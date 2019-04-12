<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\FastRoute;

use Chubbyphp\Framework\Router\InvalidParameter;
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
     * @param array                  $parameters
     *
     * @return string
     *
     * @throws UrlGeneratorException
     */
    public function generateUrl(ServerRequestInterface $request, string $name, array $parameters = []): string
    {
        $uri = $request->getUri();
        $requestTarget = $this->generatePath($name, $parameters);

        return $uri->getScheme().'://'.$uri->getAuthority().$requestTarget;
    }

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return string
     *
     * @throws UrlGeneratorException
     */
    public function generatePath(string $name, array $parameters = []): string
    {
        $route = $this->getRoute($name);

        $routePartSets = array_reverse($this->routeParser->parse($route->getPattern()));

        $routeIndex = $this->getRouteIndex($routePartSets, $parameters);

        $pathParts = [];
        foreach ($routePartSets[$routeIndex] as $routePart) {
            if (is_array($routePart)) {
                $parameter = $routePart[0];
                $pathParts[] = (string) $parameters[$parameter];
                unset($parameters[$parameter]);
            } else {
                $pathParts[] = $routePart;
            }
        }

        $path = implode('', $pathParts);

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
     * @param array $parameters
     *
     * @return int
     *
     * @throws UrlGeneratorException
     */
    private function getRouteIndex(array $routePartSets, array $parameters): int
    {
        foreach ($routePartSets as $routeIndex => $routeParts) {
            $missingParameters = [];
            $invalidParameters = [];

            foreach ($routeParts as $routePart) {
                if (is_array($routePart)) {
                    $parameter = $routePart[0];
                    if (!isset($parameters[$parameter])) {
                        $missingParameters[] = $parameter;

                        continue;
                    }

                    $pattern = $routePart[1];
                    $value = (string) $parameters[$parameter];

                    if (1 !== preg_match('/^'.str_replace('/', '\/', $pattern).'$/', $value)) {
                        $invalidParameters[] = new InvalidParameter($parameter, $value, $pattern);
                    }
                }
            }

            if ([] !== $invalidParameters) {
                throw UrlGeneratorException::createForInvalidParameters($invalidParameters);
            }

            if ([] === $missingParameters) {
                return $routeIndex;
            }
        }

        throw UrlGeneratorException::createForMissingParameters($missingParameters);
    }
}
