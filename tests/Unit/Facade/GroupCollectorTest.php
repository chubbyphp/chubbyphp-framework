<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Facade;

use Chubbyphp\Framework\Facade\GroupCollector;
use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Framework\Facade\AbstractCollector
 * @covers \Chubbyphp\Framework\Facade\GroupCollector
 *
 * @internal
 */
final class GroupCollectorTest extends TestCase
{
    public function testEmpty(): void
    {
        self::assertSame([], GroupCollector::create()->getChildren());
    }

    public function testIsImmutable(): void
    {
        $builder = new MockObjectBuilder();

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        $collector = GroupCollector::create();
        $collectorWithRoute = $collector->get('/ping', 'ping', $handler);

        self::assertNotSame($collector, $collectorWithRoute);
        self::assertSame([], $collector->getChildren());
        self::assertCount(1, $collectorWithRoute->getChildren());
    }

    public function testRoutesAndGroups(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MiddlewareInterface $middleware1 */
        $middleware1 = $builder->create(MiddlewareInterface::class, []);

        /** @var MiddlewareInterface $middleware2 */
        $middleware2 = $builder->create(MiddlewareInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        $collector = GroupCollector::create()
            ->route('TRACE', '/trace', 'trace', [$middleware1], $handler, ['tokens' => ['trace' => '\d+']])
            ->delete('/delete', 'delete', $handler, ['tokens' => ['delete' => '\d+']])
            ->get('/get', 'get', $handler)
            ->head('/head', 'head', $handler)
            ->options('/options', 'options', $handler)
            ->patch('/patch', 'patch', $handler)
            ->post('/post', 'post', $handler)
            ->put('/put', 'put', $handler)
            ->group(
                '/group',
                [$middleware1],
                static fn (GroupCollector $group) => $group
                    ->get('/{id}', 'group_get', [$middleware2], $handler, ['tokens' => ['id' => '\d+']]),
                ['tokens' => ['group' => '[a-z]+']]
            )
            ->group(
                '/other',
                static fn (GroupCollector $group) => $group
                    ->get('/{id}', 'other_get', $handler),
                ['tokens' => ['other' => '[a-z]+']]
            )
        ;

        $children = $collector->getChildren();

        self::assertCount(10, $children);

        $routes = Group::create('', $children)->getRoutes();

        self::assertCount(10, $routes);

        self::assertSame(
            [
                ['TRACE', '/trace', 'trace', [$middleware1], ['tokens' => ['trace' => '\d+']]],
                ['DELETE', '/delete', 'delete', [], ['tokens' => ['delete' => '\d+']]],
                ['GET', '/get', 'get', [], []],
                ['HEAD', '/head', 'head', [], []],
                ['OPTIONS', '/options', 'options', [], []],
                ['PATCH', '/patch', 'patch', [], []],
                ['POST', '/post', 'post', [], []],
                ['PUT', '/put', 'put', [], []],
                [
                    'GET',
                    '/group/{id}',
                    'group_get',
                    [$middleware1, $middleware2],
                    ['tokens' => ['group' => '[a-z]+', 'id' => '\d+']],
                ],
                ['GET', '/other/{id}', 'other_get', [], ['tokens' => ['other' => '[a-z]+']]],
            ],
            array_map(
                static fn (RouteInterface $route) => [
                    $route->getMethod(),
                    $route->getPath(),
                    $route->getName(),
                    $route->getMiddlewares(),
                    $route->getPathOptions(),
                ],
                $routes
            )
        );

        foreach ($routes as $route) {
            self::assertSame($handler, $route->getRequestHandler());
        }
    }

    public function testRouteWithHandlerAndInvalidThirdArgument(): void
    {
        $builder = new MockObjectBuilder();

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'route(): with the request handler as content parameter, the next one must be pathOptions (array)'
        );

        GroupCollector::create()->get('/ping', 'ping', $handler, $handler);
    }

    public function testRouteWithMiddlewaresAndMissingHandler(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'route(): with middlewares as first content parameter, the next one must be the request handler'
        );

        GroupCollector::create()->get('/ping', 'ping', []);
    }

    public function testGroupWithConfigureAndInvalidThirdArgument(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'group(): with configure as second parameter, the third one must be pathOptions (array)'
        );

        GroupCollector::create()->group(
            '/group',
            static fn (GroupCollector $group) => $group,
            static fn (GroupCollector $group) => $group
        );
    }

    public function testGroupWithMiddlewaresAndMissingConfigure(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'group(): with middlewares as second parameter, the third one must be the configure callback'
        );

        GroupCollector::create()->group('/group', []);
    }
}
