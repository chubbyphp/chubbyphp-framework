<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Router\AuraRouter;

use Aura\RouteParser;
use Chubbyphp\Framework\Router\AuraRouter\UrlGenerator;
use Chubbyphp\Framework\Router\RouteCollectionInterface;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\UrlGeneratorException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @covers \Chubbyphp\Framework\Router\AuraRouter\UrlGenerator
 */
final class UrlGeneratorTest extends TestCase
{
    use MockByCallsTrait;

    public function testGenerateUriSuccessful(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getAuthority')->with()->willReturn('user:password@localhost'),
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getAuthority')->with()->willReturn('user:password@localhost'),
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getAuthority')->with()->willReturn('user:password@localhost'),
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getAuthority')->with()->willReturn('user:password@localhost'),
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getAuthority')->with()->willReturn('user:password@localhost'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getOptions')->with()->willReturn(['tokens' => ['id' => '\d+', 'name' => '[a-z]+']]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}{/name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteCollectionInterface|MockObject $routeCollection */
        $routeCollection = $this->getMockByCalls(RouteCollectionInterface::class, [
            Call::create('getRoutes')->with()->willReturn(['user' => $route]),
        ]);

        $urlGenerator = new UrlGenerator($routeCollection);

        self::assertSame(
            'https://user:password@localhost/user/{id}',
            $urlGenerator->generateUrl($request, 'user')
        );
        self::assertSame(
            'https://user:password@localhost/user/1',
            $urlGenerator->generateUrl($request, 'user', ['id' => 1])
        );
        self::assertSame(
            'https://user:password@localhost/user/1?key=value',
            $urlGenerator->generateUrl($request, 'user', ['id' => 1], ['key' => 'value'])
        );
        self::assertSame(
            'https://user:password@localhost/user/1/sample',
            $urlGenerator->generateUrl($request, 'user', ['id' => 1, 'name' => 'sample'])
        );
        self::assertSame(
            'https://user:password@localhost/user/1/sample?key=value',
            $urlGenerator->generateUrl($request, 'user', ['id' => 1, 'name' => 'sample'], ['key' => 'value'])
        );
    }

    public function testGeneratePathWithMissingRoute(): void
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
        $urlGenerator->generatePath('user', ['id' => 1]);
    }

    public function testGeneratePathSuccessful(): void
    {
        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getOptions')->with()->willReturn(['tokens' => ['id' => '\d+', 'name' => '[a-z]+']]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}{/name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteCollectionInterface|MockObject $routeCollection */
        $routeCollection = $this->getMockByCalls(RouteCollectionInterface::class, [
            Call::create('getRoutes')->with()->willReturn(['user' => $route]),
        ]);

        $urlGenerator = new UrlGenerator($routeCollection);

        self::assertSame('/user/{id}', $urlGenerator->generatePath('user'));
        self::assertSame('/user/1', $urlGenerator->generatePath('user', ['id' => 1]));
        self::assertSame('/user/1?key=value', $urlGenerator->generatePath('user', ['id' => 1], ['key' => 'value']));
        self::assertSame('/user/1/sample', $urlGenerator->generatePath('user', ['id' => 1, 'name' => 'sample']));
        self::assertSame(
            '/user/1/sample?key=value',
            $urlGenerator->generatePath('user', ['id' => 1, 'name' => 'sample'], ['key' => 'value'])
        );
    }
}
