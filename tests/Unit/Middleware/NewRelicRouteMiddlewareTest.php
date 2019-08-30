<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware
{
    final class TestExtesionLoaded
    {
        /**
         * @var array<int, string>
         */
        private static $extensions = [];

        /**
         * @param string $name
         */
        public static function add(string $name): void
        {
            self::$extensions[] = $name;
        }

        /**
         * @return bool
         */
        public static function isLoaded(string $name): bool
        {
            return in_array($name, self::$extensions, true);
        }

        public static function reset(): void
        {
            self::$extensions = [];
        }
    }

    function extension_loaded(string $name): bool
    {
        return TestExtesionLoaded::isLoaded($name);
    }

    final class TestNewRelicNameTransaction
    {
        /**
         * @var array<string>
         */
        private static $actions = [];

        /**
         * @param string $action
         */
        public static function add(string $action): void
        {
            self::$actions[] = $action;
        }

        /**
         * @return array<string>
         */
        public static function all(): array
        {
            return self::$actions;
        }

        public static function reset(): void
        {
            self::$actions = [];
        }
    }

    function newrelic_name_transaction(string $action): void
    {
        TestNewRelicNameTransaction::add($action);
    }
}

namespace Chubbyphp\Tests\Framework\Unit\Middleware
{
    use Chubbyphp\Framework\Middleware\NewRelicRouteMiddleware;
    use Chubbyphp\Framework\Middleware\TestExtesionLoaded;
    use Chubbyphp\Framework\Middleware\TestNewRelicNameTransaction;
    use Chubbyphp\Framework\Router\RouteInterface;
    use Chubbyphp\Mock\Call;
    use Chubbyphp\Mock\MockByCallsTrait;
    use PHPUnit\Framework\MockObject\MockObject;
    use PHPUnit\Framework\TestCase;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Server\RequestHandlerInterface;

    /**
     * @covers \Chubbyphp\Framework\Middleware\NewRelicRouteMiddleware
     *
     * @internal
     */
    final class NewRelicRouteMiddlewareTest extends TestCase
    {
        use MockByCallsTrait;

        public function testWithoutNewRelicExtension(): void
        {
            TestExtesionLoaded::reset();
            TestNewRelicNameTransaction::reset();

            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class);

            /** @var ResponseInterface|MockObject $response */
            $response = $this->getMockByCalls(ResponseInterface::class);

            /** @var RequestHandlerInterface|MockObject $handler */
            $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
                Call::create('handle')->with($request)->willReturn($response),
            ]);

            $middleware = new NewRelicRouteMiddleware();

            self::assertSame($response, $middleware->process($request, $handler));

            self::assertSame([], TestNewRelicNameTransaction::all());
        }

        public function testWithNewRelicExtension(): void
        {
            TestExtesionLoaded::add('newrelic');
            TestNewRelicNameTransaction::reset();

            /** @var RouteInterface|MockObject $route */
            $route = $this->getMockByCalls(RouteInterface::class, [
                Call::create('getName')->with()->willReturn('route_name'),
            ]);

            /** @var ServerRequestInterface|MockObject $request */
            $request = $this->getMockByCalls(ServerRequestInterface::class, [
                Call::create('getAttribute')->with('route', null)->willReturn($route),
            ]);

            /** @var ResponseInterface|MockObject $response */
            $response = $this->getMockByCalls(ResponseInterface::class);

            /** @var RequestHandlerInterface|MockObject $handler */
            $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
                Call::create('handle')->with($request)->willReturn($response),
            ]);

            $middleware = new NewRelicRouteMiddleware();

            self::assertSame($response, $middleware->process($request, $handler));

            self::assertSame(['route_name'], TestNewRelicNameTransaction::all());
        }
    }
}
