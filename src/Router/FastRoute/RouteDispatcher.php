<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\FastRoute;

use Chubbyphp\Framework\Router\RouteDispatcherException;
use Chubbyphp\Framework\Router\RouteDispatcherInterface;
use Chubbyphp\Framework\Router\RouteInterface;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;
use Psr\Http\Message\ServerRequestInterface;

final class RouteDispatcher implements RouteDispatcherInterface
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
     * @param RouteInterface[] $routes
     * @param string|null      $cacheDir
     */
    public function __construct(array $routes, string $cacheDir = null)
    {
        $this->routes = $this->getRoutesByName($routes);
        $this->dispatcher = $this->getDispatcher(
            $routes,
            ($cacheDir ?? sys_get_temp_dir()).'/fast-route-'.hash('sha256', $this->routesAsString($routes)).'.php'
        );
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
     *
     * @return string
     */
    private function routesAsString(array $routes): string
    {
        $string = '';
        foreach ($routes as $route) {
            $string .= $route.PHP_EOL;
        }

        return trim($string);
    }

    /**
     * @param array  $routes
     * @param string $cacheFile
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
    public function dispatch(ServerRequestInterface $request): RouteInterface
    {
        $method = $request->getMethod();
        $path = rawurldecode($request->getUri()->getPath());

        $routeInfo = $this->dispatcher->dispatch($method, $path);

        if (Dispatcher::NOT_FOUND === $routeInfo[0]) {
            throw RouteDispatcherException::createForNotFound($request->getRequestTarget());
        }

        if (Dispatcher::METHOD_NOT_ALLOWED === $routeInfo[0]) {
            throw RouteDispatcherException::createForMethodNotAllowed(
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
}
