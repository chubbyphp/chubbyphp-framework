<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Router\FastRoute;

use Chubbyphp\Framework\Router\FastRoute\UrlGenerator;
use Chubbyphp\Framework\Router\RouteCollectionInterface;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\UrlGeneratorException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use FastRoute\RouteParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Framework\Router\FastRoute\UrlGenerator
 */
final class UrlGeneratorTest extends TestCase
{
    use MockByCallsTrait;

    public function testRequestTargetForWithMissingRoute(): void
    {
        $this->expectException(UrlGeneratorException::class);
        $this->expectExceptionMessage('Missing route: "user"');
        $this->expectExceptionCode(1);

        /** @var RouteCollectionInterface|MockObject $routeCollection */
        $routeCollection = $this->getMockByCalls(RouteCollectionInterface::class, [
            Call::create('getRoutes')->with()->willReturn([]),
        ]);

        /** @var RouteParser|MockObject $routeParser */
        $routeParser = $this->getMockByCalls(RouteParser::class);

        $urlGenerator = new UrlGenerator($routeCollection, $routeParser);
        $urlGenerator->requestTargetFor('user', ['id' => 1]);
    }

    public function testRequestTargetForWithMissingParameters(): void
    {
        $this->expectException(UrlGeneratorException::class);
        $this->expectExceptionMessage('Missing parameters: "id"');
        $this->expectExceptionCode(2);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getPattern')->with()->willReturn('/user[/{id:\d+}]/{name}'),
        ]);

        /** @var RouteCollectionInterface|MockObject $routeCollection */
        $routeCollection = $this->getMockByCalls(RouteCollectionInterface::class, [
            Call::create('getRoutes')->with()->willReturn(['user' => $route]),
        ]);

        $parsedPath = [
            [
                '/user/',
                ['id', '\\d+'],
            ],
            [
                '/user/',
                ['id', '\\d+'],
                '/',
                ['name', '[^/]+'],
            ],
        ];

        /** @var RouteParser|MockObject $routeParser */
        $routeParser = $this->getMockByCalls(RouteParser::class, [
            Call::create('parse')->with('/user[/{id:\d+}]/{name}')->willReturn($parsedPath),
        ]);

        $urlGenerator = new UrlGenerator($routeCollection, $routeParser);
        $urlGenerator->requestTargetFor('user');
    }

    public function testRequestTargetForWithInvalidParameters(): void
    {
        $this->expectException(UrlGeneratorException::class);
        $this->expectExceptionMessage(
            'Parameter "id" with value "c0b8bf5f-476b-4552-97aa-e37b8004a5c0" does not match "\d+"'
        );
        $this->expectExceptionCode(3);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getPattern')->with()->willReturn('/user[/{id:\d+}]/{name}'),
        ]);

        /** @var RouteCollectionInterface|MockObject $routeCollection */
        $routeCollection = $this->getMockByCalls(RouteCollectionInterface::class, [
            Call::create('getRoutes')->with()->willReturn(['user' => $route]),
        ]);

        $parsedPath = [
            [
                '/user/',
                ['id', '\\d+'],
            ],
            [
                '/user/',
                ['id', '\\d+'],
                '/',
                ['name', '[^/]+'],
            ],
        ];

        /** @var RouteParser|MockObject $routeParser */
        $routeParser = $this->getMockByCalls(RouteParser::class, [
            Call::create('parse')->with('/user[/{id:\d+}]/{name}')->willReturn($parsedPath),
        ]);

        $urlGenerator = new UrlGenerator($routeCollection, $routeParser);
        $urlGenerator->requestTargetFor('user', ['id' => 'c0b8bf5f-476b-4552-97aa-e37b8004a5c0']);
    }

    public function testRequestTargetForSuccessful(): void
    {
        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getPattern')->with()->willReturn('/user[/{id:\d+}]/{name}'),
            Call::create('getPattern')->with()->willReturn('/user[/{id:\d+}]/{name}'),
            Call::create('getPattern')->with()->willReturn('/user[/{id:\d+}]/{name}'),
            Call::create('getPattern')->with()->willReturn('/user[/{id:\d+}]/{name}'),
        ]);

        /** @var RouteCollectionInterface|MockObject $routeCollection */
        $routeCollection = $this->getMockByCalls(RouteCollectionInterface::class, [
            Call::create('getRoutes')->with()->willReturn(['user' => $route]),
        ]);

        $parsedPath = [
            [
                '/user/',
                ['id', '\\d+'],
            ],
            [
                '/user/',
                ['id', '\\d+'],
                '/',
                ['name', '[^/]+'],
            ],
        ];

        /** @var RouteParser|MockObject $routeParser */
        $routeParser = $this->getMockByCalls(RouteParser::class, [
            Call::create('parse')->with('/user[/{id:\d+}]/{name}')->willReturn($parsedPath),
            Call::create('parse')->with('/user[/{id:\d+}]/{name}')->willReturn($parsedPath),
            Call::create('parse')->with('/user[/{id:\d+}]/{name}')->willReturn($parsedPath),
            Call::create('parse')->with('/user[/{id:\d+}]/{name}')->willReturn($parsedPath),
        ]);

        $urlGenerator = new UrlGenerator($routeCollection, $routeParser);

        self::assertSame('/user/1', $urlGenerator->requestTargetFor('user', ['id' => 1]));
        self::assertSame('/user/1?key=value', $urlGenerator->requestTargetFor('user', ['id' => 1, 'key' => 'value']));
        self::assertSame('/user/1/sample', $urlGenerator->requestTargetFor('user', ['id' => 1, 'name' => 'sample']));
        self::assertSame(
            '/user/1/sample?key=value',
            $urlGenerator->requestTargetFor('user', ['id' => 1, 'name' => 'sample', 'key' => 'value'])
        );
    }
}
