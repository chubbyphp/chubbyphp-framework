<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router;

use Chubbyphp\Framework\Router\Exceptions\MethodNotAllowedException;
use Chubbyphp\Framework\Router\Exceptions\MissingAttributeForPathGenerationException;
use Chubbyphp\Framework\Router\Exceptions\MissingRouteByNameException;
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
        $this->router = $this->createRouter($routes);
        $this->basePath = $basePath;
    }

    public function match(ServerRequestInterface $request): RouteInterface
    {
        try {
            $sunriseRoute = $this->router->match($request);

            return Route::create(
                $request->getMethod(),
                $sunriseRoute->getPath(),
                $sunriseRoute->getName(),
                $sunriseRoute->getRequestHandler()
            )->middlewares($sunriseRoute->getMiddlewares())->withAttributes($sunriseRoute->getAttributes());
        } catch (SunriseRouteNotFoundException $exception) {
            throw NotFoundException::create($request->getRequestTarget());
        } catch (SunriseMethodNotAllowedException $exception) {
            throw MethodNotAllowedException::create(
                $request->getRequestTarget(),
                $request->getMethod(),
                $exception->getAllowedMethods()
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
        try {
            $path = $this->router->generateUri($name, $attributes, true);
        } catch (SunriseRouteNotFoundException $exception) {
            throw MissingRouteByNameException::create($name);
        } catch (SunriseMissingAttributeValueException $exception) {
            $match = $exception->fromContext('match');
            throw MissingAttributeForPathGenerationException::create($name, $match['name']);
        } catch (SunriseInvalidAttributeValueException $exception) {
            $match = $exception->fromContext('match');
            $value = $exception->fromContext('value');
            throw NotMatchingValueForPathGenerationException::create(
                $name,
                $match['name'],
                $value,
                $match['pattern']
            );
        }

        if ([] === $queryParams) {
            return $this->basePath.$path;
        }

        return $this->basePath.$path.'?'.http_build_query($queryParams);
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
                $route->getRequestHandler(),
                $route->getMiddlewares(),
                $route->getAttributes()
            ));
        }

        return $router;
    }
}
