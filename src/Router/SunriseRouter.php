<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\Exceptions\MethodNotAllowedException;
use Chubbyphp\Framework\Router\Exceptions\MissingAttributeForPathGenerationException;
use Chubbyphp\Framework\Router\Exceptions\NotFoundException;
use Chubbyphp\Framework\Router\Exceptions\NotMatchingValueForPathGenerationException;
use Psr\Http\Message\ServerRequestInterface;
use Sunrise\Http\Router\Exception\InvalidAttributeValueException as SunriseInvalidAttributeValueException;
use Sunrise\Http\Router\Exception\MethodNotAllowedException as SunriseMethodNotAllowedException;
use Sunrise\Http\Router\Exception\MissingAttributeValueException as SunriseMissingAttributeValueException;
use Sunrise\Http\Router\Exception\RouteNotFoundException as SunriseRouteNotFoundException;
use Sunrise\Http\Router\RouteFactory;
use Sunrise\Http\Router\Router;

final class SunriseRouter implements RouterInterface
{
    /**
     * @var array<RouteInterface>
     */
    private $routes = [];

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @param array<RouteInterface> $routes
     */
    public function __construct(array $routes, string $basePath = '')
    {
        $this->routes = $this->getRoutesByName($routes);
        $this->router = $this->createRouter($routes);
        $this->basePath = $basePath;
    }

    public function match(ServerRequestInterface $request): RouteInterface
    {
        try {
            $sunriseRoute = $this->router->match($request);

            /** @var RouteInterface $route */
            $route = $this->routes[$sunriseRoute->getName()];

            return $route->withAttributes($sunriseRoute->getAttributes());
        } catch (SunriseRouteNotFoundException $exception) {
            throw NotFoundException::create($request->getRequestTarget());
        } catch (SunriseMethodNotAllowedException $exception) {
            throw MethodNotAllowedException::create(
                $request->getRequestTarget(),
                $request->getMethod(),
                $exception->getAllowedMethods(),
            );
        }
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
        if (!isset($this->routes[$name])) {
            throw RouterException::createForMissingRoute($name);
        }

        try {
            $path = $this->router->generateUri($name, $attributes, true);
        } catch (SunriseMissingAttributeValueException $exception) {
            throw MissingAttributeForPathGenerationException::create($name, 'attribute');
        } catch (SunriseInvalidAttributeValueException $exception) {
            throw NotMatchingValueForPathGenerationException::create($name, 'attribute', 'value', 'pattern');
        }

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
    private function createRouter(array $routes): Router
    {
        $routeFactory = new RouteFactory();
        $router = new Router();

        foreach ($routes as $route) {
            $router->addRoute($routeFactory->createRoute(
                $route->getName(),
                $route->getPath(),
                [$route->getMethod()],
                new CallbackRequestHandler(function (): void {}),
                [],
                $route->getAttributes()
            ));
        }

        return $router;
    }
}
