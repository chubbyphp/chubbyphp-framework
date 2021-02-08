<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Aura\Router\Route;
use Chubbyphp\Framework\Router\AuraRouter;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouterException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @covers \Chubbyphp\Framework\Router\AuraRouter
 *
 * @internal
 */
final class AuraRouterTest extends TestCase
{
    use MockByCallsTrait;

    const UUID_PATTERN = '[0-9a-f]{8}\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\b[0-9a-f]{12}';

    public function testMatchFound(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()->willReturn([]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('withAttributes')->with([])->willReturnSelf(),
        ]);

        $router = new AuraRouter([$route]);

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
            Call::create('getPath')->with()->willReturn('/'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getRequestTarget')->with()->willReturn('/'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()->willReturn([]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new AuraRouter([$route]);

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
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('POST'),
            Call::create('getMethod')->with()->willReturn('POST'),
            Call::create('getRequestTarget')->with()->willReturn('/api/pets?offset=1&limit=20'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()->willReturn([]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new AuraRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testMatchWithTokensNotMatch(): void
    {
        $this->expectException(RouterException::class);
        $this->expectExceptionMessage(
            'The page "/api/pets/1" you are looking for could not be found.'
                .' Check the address bar to ensure your URL is spelled correctly.'
        );
        $this->expectExceptionCode(404);

        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getPath')->with()->willReturn('/api/pets/1'),
            Call::create('getPath')->with()->willReturn('/api/pets/1'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getRequestTarget')->with()->willReturn('/api/pets/1'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_read'),
            Call::create('getPathOptions')->with()->willReturn(['tokens' => ['id' => self::UUID_PATTERN]]),
            Call::create('getName')->with()->willReturn('pet_read'),
            Call::create('getPath')->with()->willReturn('/api/pets/{id}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new AuraRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testMatchWithTokensMatch(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getPath')->with()->willReturn('/api/pets/8b72750c-5306-416c-bba7-5b41f1c44791'),
            Call::create('getPath')->with()->willReturn('/api/pets/8b72750c-5306-416c-bba7-5b41f1c44791'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_read'),
            Call::create('getPathOptions')->with()->willReturn(['tokens' => ['id' => self::UUID_PATTERN]]),
            Call::create('getName')->with()->willReturn('pet_read'),
            Call::create('getPath')->with()->willReturn('/api/pets/{id}'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('withAttributes')->with(['id' => '8b72750c-5306-416c-bba7-5b41f1c44791'])->willReturnSelf(),
        ]);

        $router = new AuraRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testMatchWithDefaultsMatch(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()
                ->willReturn(['tokens' => ['format' => '(\.[^/]+)?'], 'defaults' => ['format' => '.html']]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets{format}'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('withAttributes')->with(['format' => '.html'])->willReturnSelf(),
        ]);

        $router = new AuraRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testMatchWithHostMatch(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getHost')->with()->willReturn('test.development'),
            Call::create('getPath')->with()->willReturn('/'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPathOptions')->with()->willReturn(['host' => 'test.development']),
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('withAttributes')->with([])->willReturnSelf(),
        ]);

        $router = new AuraRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testMatchWithHostNotMatch(): void
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
            Call::create('getHost')->with()->willReturn('test2.development'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getRequestTarget')->with()->willReturn('/'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPathOptions')->with()->willReturn(['host' => 'test.development']),
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new AuraRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testMatchWithSecureMatch(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getPath')->with()->willReturn('/'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getServerParams')->with()->willReturn(['HTTPS' => true]),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPathOptions')->with()->willReturn(['secure' => true]),
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('withAttributes')->with([])->willReturnSelf(),
        ]);

        $router = new AuraRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testMatchWithSecureNotMatch(): void
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
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getServerParams')->with()->willReturn(['HTTPS' => false]),
            Call::create('getRequestTarget')->with()->willReturn('/'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPathOptions')->with()->willReturn(['secure' => true]),
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new AuraRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testMatchWithSpecialMatch(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getPath')->with()->willReturn('/'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPathOptions')->with()
                ->willReturn(['special' => static function (ServerRequestInterface $request, Route $route) {
                    return true;
                }]),
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('withAttributes')->with([])->willReturnSelf(),
        ]);

        $router = new AuraRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testMatchWithSpecialNotMatch(): void
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
            Call::create('getPath')->with()->willReturn('/'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getRequestTarget')->with()->willReturn('/'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPathOptions')->with()
                ->willReturn(['special' => static function (ServerRequestInterface $request, Route $route) {
                    return false;
                }]),
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new AuraRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testMatchWithWildcardMatch(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getPath')->with()->willReturn('/part1/part2/part3'),
            Call::create('getPath')->with()->willReturn('/part1/part2/part3'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPathOptions')->with()->willReturn(['wildcard' => 'parts']),
            Call::create('getName')->with()->willReturn('index'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('withAttributes')->with(['parts' => ['part1', 'part2', 'part3']])->willReturnSelf(),
        ]);

        $router = new AuraRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testGenerateUri(): void
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
            Call::create('getPathOptions')->with()->willReturn(['tokens' => ['id' => '\d+', 'name' => '[a-z]+']]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}{/name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new AuraRouter([$route]);

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
            'https://user:password@localhost/user/1/sample?key1=value1&key2=value2',
            $router->generateUrl(
                $request,
                'user',
                ['id' => 1, 'name' => 'sample'],
                ['key1' => 'value1', 'key2' => 'value2']
            )
        );
    }

    public function testGenerateUriWithBasePath(): void
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
            Call::create('getPathOptions')->with()->willReturn(['tokens' => ['id' => '\d+', 'name' => '[a-z]+']]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}{/name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new AuraRouter([$route], '/path/to/directory');

        self::assertSame(
            'https://user:password@localhost/path/to/directory/user/{id}',
            $router->generateUrl($request, 'user')
        );
        self::assertSame(
            'https://user:password@localhost/path/to/directory/user/1',
            $router->generateUrl($request, 'user', ['id' => 1])
        );
        self::assertSame(
            'https://user:password@localhost/path/to/directory/user/1?key=value',
            $router->generateUrl($request, 'user', ['id' => 1], ['key' => 'value'])
        );
        self::assertSame(
            'https://user:password@localhost/path/to/directory/user/1/sample',
            $router->generateUrl($request, 'user', ['id' => 1, 'name' => 'sample'])
        );
        self::assertSame(
            'https://user:password@localhost/path/to/directory/user/1/sample?key1=value1&key2=value2',
            $router->generateUrl(
                $request,
                'user',
                ['id' => 1, 'name' => 'sample'],
                ['key1' => 'value1', 'key2' => 'value2']
            )
        );
    }

    public function testGeneratePathWithMissingRoute(): void
    {
        $this->expectException(RouterException::class);
        $this->expectExceptionMessage('Missing route: "user"');
        $this->expectExceptionCode(1);

        $router = new AuraRouter([]);
        $router->generatePath('user', ['id' => 1]);
    }

    public function testGeneratePathSuccessful(): void
    {
        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPathOptions')->with()->willReturn(['tokens' => ['id' => '\d+', 'name' => '[a-z]+']]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}{/name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new AuraRouter([$route]);

        self::assertSame('/user/{id}', $router->generatePath('user'));
        self::assertSame('/user/1', $router->generatePath('user', ['id' => 1]));
        self::assertSame('/user/1?key=value', $router->generatePath('user', ['id' => 1], ['key' => 'value']));
        self::assertSame('/user/1/sample', $router->generatePath('user', ['id' => 1, 'name' => 'sample']));
        self::assertSame(
            '/user/1/sample?key1=value1&key2=value2',
            $router->generatePath(
                'user',
                ['id' => 1, 'name' => 'sample'],
                ['key1' => 'value1', 'key2' => 'value2']
            )
        );
    }

    public function testGeneratePathWithBasePath(): void
    {
        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPathOptions')->with()->willReturn(['tokens' => ['id' => '\d+', 'name' => '[a-z]+']]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}{/name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new AuraRouter([$route], '/path/to/directory');

        self::assertSame('/path/to/directory/user/{id}', $router->generatePath('user'));
        self::assertSame('/path/to/directory/user/1', $router->generatePath('user', ['id' => 1]));
        self::assertSame(
            '/path/to/directory/user/1?key=value',
            $router->generatePath('user', ['id' => 1], ['key' => 'value'])
        );
        self::assertSame(
            '/path/to/directory/user/1/sample',
            $router->generatePath('user', ['id' => 1, 'name' => 'sample'])
        );
        self::assertSame(
            '/path/to/directory/user/1/sample?key1=value1&key2=value2',
            $router->generatePath(
                'user',
                ['id' => 1, 'name' => 'sample'],
                ['key1' => 'value1', 'key2' => 'value2']
            )
        );
    }
}
