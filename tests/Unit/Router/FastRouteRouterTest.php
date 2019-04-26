<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouterException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @covers \Chubbyphp\Framework\Router\FastRouteRouter
 */
final class FastRouteRouterTest extends TestCase
{
    use MockByCallsTrait;

    public function testMatchFound(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('__toString')->with()->willReturn('/api/pets::[]::GET::pet_list'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('withAttributes')->with([])->willReturnSelf(),
        ]);

        $cacheDir = sys_get_temp_dir().'/fast-route/'.uniqid().'/'.uniqid();

        mkdir($cacheDir, 0777, true);

        $router = new FastRouteRouter([$route], $cacheDir);

        self::assertFileExists(
            sprintf(
                '%s/fast-route-60604cc89fbb413657da7514fb584cb803fc10fad04d651a689f8fb704682566.php',
                $cacheDir
            )
        );

        self::assertSame($route, $router->match($request));
    }

    public function testMatchNotFound(): void
    {
        $this->expectException(RouterException::class);
        $this->expectExceptionMessage(
            'The page "/" you are looking for could not be found.'
                .' Check the address bar to ensure your URL is spelled correctly.'
        );
        $this->expectExceptionCode(404);

        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getPath')->with()->willReturn('/'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getRequestTarget')->with()->willReturn('/'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('__toString')->with()->willReturn('/api/pets::[]::GET::pet_list'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getName')->with()->willReturn('pet_list'),
        ]);

        $cacheDir = sys_get_temp_dir().'/fast-route/'.uniqid().'/'.uniqid();

        mkdir($cacheDir, 0777, true);

        $router = new FastRouteRouter([$route], $cacheDir);

        self::assertFileExists(
            sprintf(
                '%s/fast-route-60604cc89fbb413657da7514fb584cb803fc10fad04d651a689f8fb704682566.php',
                $cacheDir
            )
        );

        self::assertSame($route, $router->match($request));
    }

    public function testMatchMethodNotAllowed(): void
    {
        $this->expectException(RouterException::class);
        $this->expectExceptionMessage(
            'Method "POST" at path "/api/pets?offset=1&limit=20" is not allowed. Must be one of: "GET"'
        );
        $this->expectExceptionCode(405);

        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getMethod')->with()->willReturn('POST'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getRequestTarget')->with()->willReturn('/api/pets?offset=1&limit=20'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('__toString')->with()->willReturn('/api/pets::[]::GET::pet_list'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getName')->with()->willReturn('pet_list'),
        ]);

        $cacheDir = sys_get_temp_dir().'/fast-route/'.uniqid().'/'.uniqid();

        mkdir($cacheDir, 0777, true);

        $router = new FastRouteRouter([$route], $cacheDir);

        self::assertFileExists(
            sprintf(
                '%s/fast-route-60604cc89fbb413657da7514fb584cb803fc10fad04d651a689f8fb704682566.php',
                $cacheDir
            )
        );

        self::assertSame($route, $router->match($request));
    }

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
            Call::create('getName')->with()->willReturn('user'),
            Call::create('__toString')->with()->willReturn('/user/{id:\d+}[/{name}]::[]::GET::user'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getPath')->with()->willReturn('/user/{id:\d+}[/{name}]'),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id:\d+}[/{name}]'),
            Call::create('getPath')->with()->willReturn('/user/{id:\d+}[/{name}]'),
            Call::create('getPath')->with()->willReturn('/user/{id:\d+}[/{name}]'),
            Call::create('getPath')->with()->willReturn('/user/{id:\d+}[/{name}]'),
            Call::create('getPath')->with()->willReturn('/user/{id:\d+}[/{name}]'),
        ]);

        $cacheDir = sys_get_temp_dir().'/fast-route/'.uniqid().'/'.uniqid();

        mkdir($cacheDir, 0777, true);

        $router = new FastRouteRouter([$route], $cacheDir);

        self::assertFileExists(
            sprintf(
                '%s/fast-route-2ef8ecb8c9627c24ae92c4a5092ef35648ac9a2e8ffae6deeaeb0ed5739ee1b2.php',
                $cacheDir
            )
        );

        self::assertSame(
            'https://user:password@localhost/user/{id}',
            $router->generateUrl($request, 'user')
        );
        self::assertSame(
            'https://user:password@localhost/user/1',
            $router->generateUrl($request, 'user', ['id' => 1])
        );
        self::assertSame(
            'https://user:password@localhost/user/1?key=value',
            $router->generateUrl($request, 'user', ['id' => 1], ['key' => 'value'])
        );
        self::assertSame(
            'https://user:password@localhost/user/1/sample',
            $router->generateUrl($request, 'user', ['id' => 1, 'name' => 'sample'])
        );
        self::assertSame(
            'https://user:password@localhost/user/1/sample?key=value',
            $router->generateUrl($request, 'user', ['id' => 1, 'name' => 'sample'], ['key' => 'value'])
        );
    }

    public function testGeneratePathWithMissingRoute(): void
    {
        $this->expectException(RouterException::class);
        $this->expectExceptionMessage('Missing route: "user"');
        $this->expectExceptionCode(1);

        $cacheDir = sys_get_temp_dir().'/fast-route/'.uniqid().'/'.uniqid();

        mkdir($cacheDir, 0777, true);

        $router = new FastRouteRouter([], $cacheDir);

        self::assertFileExists(
            sprintf(
                '%s/fast-route-e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855.php',
                $cacheDir
            )
        );

        $router->generatePath('user', ['id' => 1]);
    }

    public function testGeneratePathSuccessful(): void
    {
        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('user'),
            Call::create('__toString')->with()->willReturn('/user/{id:\d+}[/{name}]::[]::GET::user'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getPath')->with()->willReturn('/user/{id:\d+}[/{name}]'),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id:\d+}[/{name}]'),
            Call::create('getPath')->with()->willReturn('/user/{id:\d+}[/{name}]'),
            Call::create('getPath')->with()->willReturn('/user/{id:\d+}[/{name}]'),
            Call::create('getPath')->with()->willReturn('/user/{id:\d+}[/{name}]'),
            Call::create('getPath')->with()->willReturn('/user/{id:\d+}[/{name}]'),
        ]);

        $cacheDir = sys_get_temp_dir().'/fast-route/'.uniqid().'/'.uniqid();

        mkdir($cacheDir, 0777, true);

        $router = new FastRouteRouter([$route], $cacheDir);

        self::assertFileExists(
            sprintf(
                '%s/fast-route-2ef8ecb8c9627c24ae92c4a5092ef35648ac9a2e8ffae6deeaeb0ed5739ee1b2.php',
                $cacheDir
            )
        );

        self::assertSame('/user/{id}', $router->generatePath('user'));
        self::assertSame('/user/1', $router->generatePath('user', ['id' => 1]));
        self::assertSame('/user/1?key=value', $router->generatePath('user', ['id' => 1], ['key' => 'value']));
        self::assertSame('/user/1/sample', $router->generatePath('user', ['id' => 1, 'name' => 'sample']));
        self::assertSame(
            '/user/1/sample?key=value',
            $router->generatePath('user', ['id' => 1, 'name' => 'sample'], ['key' => 'value'])
        );
    }
}
