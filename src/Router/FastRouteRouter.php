<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;
use Psr\Http\Message\ServerRequestInterface;

final class FastRouteRouter implements RouterInterface
{
    /**
     * @var array<RouteInterface>
     */
    private $routes;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var RouteParser
     */
    private $routeParser;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @param array<RouteInterface> $routes
     */
    public function __construct(array $routes, ?string $cacheFile = null, string $basePath = '')
    {
        $this->routes = $this->getRoutesByName($routes);
        $this->dispatcher = $this->getDispatcher($routes, $cacheFile);
        $this->routeParser = new RouteParser();
        $this->basePath = $basePath;
    }

    public function match(ServerRequestInterface $request): RouteInterface
    {
        $method = $request->getMethod();
        $path = rawurldecode($request->getUri()->getPath());

        $routeInfo = $this->dispatcher->dispatch($method, $path);

        if (Dispatcher::NOT_FOUND === $routeInfo[0]) {
            throw RouterException::createForNotFound($request->getRequestTarget());
        }

        if (Dispatcher::METHOD_NOT_ALLOWED === $routeInfo[0]) {
            throw RouterException::createForMethodNotAllowed(
                $method,
                $routeInfo[1],
                $request->getRequestTarget()
            );
        }

        /** @var RouteInterface $route */
        $route = $this->routes[$routeInfo[1]];

        return $route->withAttributes($routeInfo[2]);
    }

    /**
     * @param array<string, string> $attributes
     * @param array<string, mixed>  $queryParams
     *
     * @throws RouterException
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
     * @param array<string, string> $attributes
     * @param array<string, mixed>  $queryParams
     *
     * @throws RouterException
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
            return $this->basePath.$path;
        }

        return $this->basePath.$path.'?'.http_build_query($queryParams);
    }

    /**
     * @param array<RouteInterface> $routes
     *
     * @return array<RouteInterface>
     */
    private function getRoutesByName(array $routes): array
    {
        $routesByName = [];
        foreach ($routes as $route) {
            $routesByName[$route->getName()] = $route;
        }

        return $routesByName;
    }

    /**
     * @param array<RouteInterface> $routes
     */
    private function getDispatcher(array $routes, ?string $cacheFile = null): Dispatcher
    {
        if (null === $cacheFile) {
            return new Dispatcher($this->getRouteCollector($routes)->getData());
        }

        if (!file_exists($cacheFile)) {
            file_put_contents(
                $cacheFile,
                '<?php return '.var_export($this->getRouteCollector($routes)->getData(), true).';'
            );
        }

        return new Dispatcher(require $cacheFile);
    }

    /**
     * @param array<RouteInterface> $routes
     */
    private function getRouteCollector(array $routes): RouteCollector
    {
        $routeCollector = new RouteCollector(new RouteParser(), new DataGenerator());
        foreach ($routes as $route) {
            $routeCollector->addRoute($route->getMethod(), $route->getPath(), $route->getName());
        }

        return $routeCollector;
    }

    private function getRoute(string $name): RouteInterface
    {
        if (!isset($this->routes[$name])) {
            throw RouterException::createForMissingRoute($name);
        }

        return $this->routes[$name];
    }

    /**
     * @param array<int, array<int, array|string>> $routePartSets
     * @param array<string>                        $attributes
     */
    private function getRouteIndex(array $routePartSets, array $attributes): int
    {
        foreach ($routePartSets as $routeIndex => $routeParts) {
            foreach ($routeParts as $routePart) {
                if (is_array($routePart)) {
                    $parameter = $routePart[0];
                    if (!isset($attributes[$parameter])) {
                        continue 2;
                    }
                }
            }

            return $routeIndex;
        }

        return $routeIndex;
    }
}
