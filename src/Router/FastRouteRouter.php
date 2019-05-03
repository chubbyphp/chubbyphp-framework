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
     * @var RouteInterface[]
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
     * @param RouteInterface[] $routes
     * @param string|null      $cacheFile
     */
    public function __construct(array $routes, string $cacheFile = null)
    {
        $this->routes = $this->getRoutesByName($routes);
        $this->dispatcher = $this->getDispatcher(
            $routes,
            $cacheFile ?? tempnam(sys_get_temp_dir(), 'fast-route-').'.php'
        );
        $this->routeParser = new RouteParser();
    }

    /**
     * @param RouteInterface[] $routes
     *
     * @return RouteInterface[]
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
     * @param RouteInterface[] $routes
     * @param string           $cacheFile
     *
     * @return Dispatcher
     */
    private function getDispatcher(array $routes, string $cacheFile): Dispatcher
    {
        if (!file_exists($cacheFile)) {
            $routeCollector = new RouteCollector(new RouteParser(), new DataGenerator());
            foreach ($routes as $route) {
                $routeCollector->addRoute($route->getMethod(), $route->getPath(), $route->getName());
            }

            file_put_contents($cacheFile, '<?php return '.var_export($routeCollector->getData(), true).';');
        }

        return new Dispatcher(require $cacheFile);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return RouteInterface
     */
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
        $route = $route->withAttributes($routeInfo[2]);

        return $route;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string                 $name
     * @param string[]               $attributes
     * @param array                  $queryParams
     *
     * @return string
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
     * @param string   $name
     * @param string[] $attributes
     * @param array    $queryParams
     *
     * @return string
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
            return $path;
        }

        return $path.'?'.http_build_query($queryParams);
    }

    /**
     * @param string $name
     *
     * @return RouteInterface
     *
     * @throws RouterException
     */
    private function getRoute(string $name): RouteInterface
    {
        if (!isset($this->routes[$name])) {
            throw RouterException::createForMissingRoute($name);
        }

        return $this->routes[$name];
    }

    /**
     * @param array    $routePartSets
     * @param string[] $attributes
     *
     * @return int
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
