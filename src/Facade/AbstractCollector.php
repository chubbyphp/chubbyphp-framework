<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Facade;

use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\GroupInterface;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Immutable route / group collector: every call returns a new instance, so use the return value.
 */
abstract class AbstractCollector
{
    /**
     * @param list<GroupInterface|RouteInterface> $children
     */
    protected function __construct(protected readonly array $children) {}

    /**
     * Overloads (mirroring the typescript facade), middlewares are optional and given directly before the
     * request handler they wrap:
     *  - route($method, $path, $name, $requestHandler, $pathOptions = [])
     *  - route($method, $path, $name, $middlewares, $requestHandler, $pathOptions = []).
     *
     * @param array<MiddlewareInterface>|RequestHandlerInterface $middlewaresOrRequestHandler
     * @param null|array<string, mixed>|RequestHandlerInterface  $requestHandlerOrPathOptions
     * @param array<string, mixed>                               $pathOptions
     */
    final public function route(
        string $method,
        string $path,
        string $name,
        array|RequestHandlerInterface $middlewaresOrRequestHandler,
        array|RequestHandlerInterface|null $requestHandlerOrPathOptions = null,
        array $pathOptions = []
    ): static {
        [$middlewares, $requestHandler, $resolvedPathOptions] = self::resolveRouteArguments(
            $middlewaresOrRequestHandler,
            $requestHandlerOrPathOptions,
            $pathOptions
        );

        return $this->withChildren([
            ...$this->children,
            Route::create($method, $path, $name, $requestHandler, $middlewares, $resolvedPathOptions),
        ]);
    }

    /**
     * @param array<MiddlewareInterface>|RequestHandlerInterface $middlewaresOrRequestHandler
     * @param null|array<string, mixed>|RequestHandlerInterface  $requestHandlerOrPathOptions
     * @param array<string, mixed>                               $pathOptions
     */
    final public function delete(
        string $path,
        string $name,
        array|RequestHandlerInterface $middlewaresOrRequestHandler,
        array|RequestHandlerInterface|null $requestHandlerOrPathOptions = null,
        array $pathOptions = []
    ): static {
        return $this->route(
            'DELETE',
            $path,
            $name,
            $middlewaresOrRequestHandler,
            $requestHandlerOrPathOptions,
            $pathOptions
        );
    }

    /**
     * @param array<MiddlewareInterface>|RequestHandlerInterface $middlewaresOrRequestHandler
     * @param null|array<string, mixed>|RequestHandlerInterface  $requestHandlerOrPathOptions
     * @param array<string, mixed>                               $pathOptions
     */
    final public function get(
        string $path,
        string $name,
        array|RequestHandlerInterface $middlewaresOrRequestHandler,
        array|RequestHandlerInterface|null $requestHandlerOrPathOptions = null,
        array $pathOptions = []
    ): static {
        return $this->route(
            'GET',
            $path,
            $name,
            $middlewaresOrRequestHandler,
            $requestHandlerOrPathOptions,
            $pathOptions
        );
    }

    /**
     * @param array<MiddlewareInterface>|RequestHandlerInterface $middlewaresOrRequestHandler
     * @param null|array<string, mixed>|RequestHandlerInterface  $requestHandlerOrPathOptions
     * @param array<string, mixed>                               $pathOptions
     */
    final public function head(
        string $path,
        string $name,
        array|RequestHandlerInterface $middlewaresOrRequestHandler,
        array|RequestHandlerInterface|null $requestHandlerOrPathOptions = null,
        array $pathOptions = []
    ): static {
        return $this->route(
            'HEAD',
            $path,
            $name,
            $middlewaresOrRequestHandler,
            $requestHandlerOrPathOptions,
            $pathOptions
        );
    }

    /**
     * @param array<MiddlewareInterface>|RequestHandlerInterface $middlewaresOrRequestHandler
     * @param null|array<string, mixed>|RequestHandlerInterface  $requestHandlerOrPathOptions
     * @param array<string, mixed>                               $pathOptions
     */
    final public function options(
        string $path,
        string $name,
        array|RequestHandlerInterface $middlewaresOrRequestHandler,
        array|RequestHandlerInterface|null $requestHandlerOrPathOptions = null,
        array $pathOptions = []
    ): static {
        return $this->route(
            'OPTIONS',
            $path,
            $name,
            $middlewaresOrRequestHandler,
            $requestHandlerOrPathOptions,
            $pathOptions
        );
    }

    /**
     * @param array<MiddlewareInterface>|RequestHandlerInterface $middlewaresOrRequestHandler
     * @param null|array<string, mixed>|RequestHandlerInterface  $requestHandlerOrPathOptions
     * @param array<string, mixed>                               $pathOptions
     */
    final public function patch(
        string $path,
        string $name,
        array|RequestHandlerInterface $middlewaresOrRequestHandler,
        array|RequestHandlerInterface|null $requestHandlerOrPathOptions = null,
        array $pathOptions = []
    ): static {
        return $this->route(
            'PATCH',
            $path,
            $name,
            $middlewaresOrRequestHandler,
            $requestHandlerOrPathOptions,
            $pathOptions
        );
    }

    /**
     * @param array<MiddlewareInterface>|RequestHandlerInterface $middlewaresOrRequestHandler
     * @param null|array<string, mixed>|RequestHandlerInterface  $requestHandlerOrPathOptions
     * @param array<string, mixed>                               $pathOptions
     */
    final public function post(
        string $path,
        string $name,
        array|RequestHandlerInterface $middlewaresOrRequestHandler,
        array|RequestHandlerInterface|null $requestHandlerOrPathOptions = null,
        array $pathOptions = []
    ): static {
        return $this->route(
            'POST',
            $path,
            $name,
            $middlewaresOrRequestHandler,
            $requestHandlerOrPathOptions,
            $pathOptions
        );
    }

    /**
     * @param array<MiddlewareInterface>|RequestHandlerInterface $middlewaresOrRequestHandler
     * @param null|array<string, mixed>|RequestHandlerInterface  $requestHandlerOrPathOptions
     * @param array<string, mixed>                               $pathOptions
     */
    final public function put(
        string $path,
        string $name,
        array|RequestHandlerInterface $middlewaresOrRequestHandler,
        array|RequestHandlerInterface|null $requestHandlerOrPathOptions = null,
        array $pathOptions = []
    ): static {
        return $this->route(
            'PUT',
            $path,
            $name,
            $middlewaresOrRequestHandler,
            $requestHandlerOrPathOptions,
            $pathOptions
        );
    }

    /**
     * Overloads (mirroring the typescript facade), middlewares are optional and given directly before the
     * configure callback they wrap:
     *  - group($path, $configure, $pathOptions = [])
     *  - group($path, $middlewares, $configure, $pathOptions = []).
     *
     * @param array<MiddlewareInterface>|callable(GroupCollector): GroupCollector $middlewaresOrConfigure
     * @param null|array<string, mixed>|callable(GroupCollector): GroupCollector  $configureOrPathOptions
     * @param array<string, mixed>                                                $pathOptions
     */
    final public function group(
        string $path,
        array|callable $middlewaresOrConfigure,
        array|callable|null $configureOrPathOptions = null,
        array $pathOptions = []
    ): static {
        [$middlewares, $configure, $resolvedPathOptions] = self::resolveGroupArguments(
            $middlewaresOrConfigure,
            $configureOrPathOptions,
            $pathOptions
        );

        return $this->withChildren([
            ...$this->children,
            Group::create(
                $path,
                $configure(GroupCollector::create())->getChildren(),
                $middlewares,
                $resolvedPathOptions
            ),
        ]);
    }

    /**
     * @param list<GroupInterface|RouteInterface> $children
     */
    abstract protected function withChildren(array $children): static;

    /**
     * @param array<MiddlewareInterface>|callable(GroupCollector): GroupCollector $middlewaresOrConfigure
     * @param null|array<string, mixed>|callable(GroupCollector): GroupCollector  $configureOrPathOptions
     * @param array<string, mixed>                                                $pathOptions
     *
     * @return array{
     *     0: array<MiddlewareInterface>,
     *     1: callable(GroupCollector): GroupCollector,
     *     2: array<string, mixed>
     * }
     */
    private static function resolveGroupArguments(
        array|callable $middlewaresOrConfigure,
        array|callable|null $configureOrPathOptions,
        array $pathOptions
    ): array {
        if (\is_callable($middlewaresOrConfigure)) {
            if (\is_callable($configureOrPathOptions)) {
                throw new \InvalidArgumentException(
                    'group(): with configure as second parameter, the third one must be pathOptions (array)'
                );
            }

            $resolvedPathOptions = $configureOrPathOptions ?? [];

            return [[], $middlewaresOrConfigure, $resolvedPathOptions];
        }

        if (!\is_callable($configureOrPathOptions)) {
            throw new \InvalidArgumentException(
                'group(): with middlewares as second parameter, the third one must be the configure callback'
            );
        }

        return [$middlewaresOrConfigure, $configureOrPathOptions, $pathOptions];
    }

    /**
     * @param array<MiddlewareInterface>|RequestHandlerInterface $middlewaresOrRequestHandler
     * @param null|array<string, mixed>|RequestHandlerInterface  $requestHandlerOrPathOptions
     * @param array<string, mixed>                               $pathOptions
     *
     * @return array{0: array<MiddlewareInterface>, 1: RequestHandlerInterface, 2: array<string, mixed>}
     */
    private static function resolveRouteArguments(
        array|RequestHandlerInterface $middlewaresOrRequestHandler,
        array|RequestHandlerInterface|null $requestHandlerOrPathOptions,
        array $pathOptions
    ): array {
        if ($middlewaresOrRequestHandler instanceof RequestHandlerInterface) {
            if (!\is_array($requestHandlerOrPathOptions) && null !== $requestHandlerOrPathOptions) {
                throw new \InvalidArgumentException(
                    'route(): with the request handler as content parameter, the next one must be pathOptions (array)'
                );
            }

            return [[], $middlewaresOrRequestHandler, $requestHandlerOrPathOptions ?? []];
        }

        if (!$requestHandlerOrPathOptions instanceof RequestHandlerInterface) {
            throw new \InvalidArgumentException(
                'route(): with middlewares as first content parameter, the next one must be the request handler'
            );
        }

        return [$middlewaresOrRequestHandler, $requestHandlerOrPathOptions, $pathOptions];
    }
}
