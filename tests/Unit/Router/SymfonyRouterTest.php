<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Framework\Unit\Router;

use Chubbyphp\Framework\Router\Exceptions\MethodNotAllowedException;
use Chubbyphp\Framework\Router\Exceptions\MissingAttributeForPathGenerationException;
use Chubbyphp\Framework\Router\Exceptions\MissingRouteByNameException;
use Chubbyphp\Framework\Router\Exceptions\NotFoundException;
use Chubbyphp\Framework\Router\Exceptions\NotMatchingValueForPathGenerationException;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\SymfonyRouter;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @covers \Chubbyphp\Framework\Router\SymfonyRouter
 *
 * @internal
 */
final class SymfonyRouterTest extends TestCase
{
    use MockByCallsTrait;

    const UUID_PATTERN = '[0-9a-f]{8}\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\b[0-9a-f]{12}';

    public function testMatchFound(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('localhost'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
        ]);

        /** @var RouteInterface|MockObject $route1 */
        $route1 = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_create'),
            Call::create('getPathOptions')->with()->willReturn([]),
            Call::create('getName')->with()->willReturn('pet_create'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('POST'),
        ]);

        /** @var RouteInterface|MockObject $route2 */
        $route2 = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()->willReturn([]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('withAttributes')->with([])->willReturnSelf(),
        ]);

        $cacheFile = sys_get_temp_dir().'/symfony-'.uniqid().uniqid().'.php';

        self::assertFileNotExists($cacheFile);

        $router = new SymfonyRouter([$route1, $route2], $cacheFile);

        self::assertFileExists($cacheFile);

        self::assertSame($route2, $router->match($request));

        unlink($cacheFile);
    }

    public function testMatchNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'The page "/" you are looking for could not be found.'
                .' Check the address bar to ensure your URL is spelled correctly.'
        );
        $this->expectExceptionCode(404);

        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getPath')->with()->willReturn('/'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
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

        $router = new SymfonyRouter([$route]);
        $router->match($request);
    }

    public function testMatchMethodNotAllowed(): void
    {
        $this->expectException(MethodNotAllowedException::class);
        $this->expectExceptionMessage(
            'Method "POST" at path "/api/pets?offset=1&limit=20" is not allowed. Must be one of: "GET"'
        );
        $this->expectExceptionCode(405);

        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('localhost'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('POST'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getRequestTarget')->with()->willReturn('/api/pets?offset=1&limit=20'),
            Call::create('getMethod')->with()->willReturn('POST'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()->willReturn([]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new SymfonyRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testMatchWithTokensMatch(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('localhost'),
            Call::create('getPath')->with()->willReturn('/api/pets/8b72750c-5306-416c-bba7-5b41f1c44791'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getPath')->with()->willReturn('/api/pets/8b72750c-5306-416c-bba7-5b41f1c44791'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_read'),
            Call::create('getPathOptions')->with()->willReturn([SymfonyRouter::PATH_REQUIREMENTS => ['id' => self::UUID_PATTERN]]),
            Call::create('getName')->with()->willReturn('pet_read'),
            Call::create('getPath')->with()->willReturn('/api/pets/{id}'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('withAttributes')->with(['id' => '8b72750c-5306-416c-bba7-5b41f1c44791'])->willReturnSelf(),
        ]);

        $router = new SymfonyRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testMatchWithTokensNotMatch(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'The page "/api/pets/1" you are looking for could not be found.'
                .' Check the address bar to ensure your URL is spelled correctly.'
        );
        $this->expectExceptionCode(404);

        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('localhost'),
            Call::create('getPath')->with()->willReturn('/api/pets/1'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getPath')->with()->willReturn('/api/pets/1'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getRequestTarget')->with()->willReturn('/api/pets/1'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_read'),
            Call::create('getPathOptions')->with()->willReturn([SymfonyRouter::PATH_REQUIREMENTS => ['id' => self::UUID_PATTERN]]),
            Call::create('getName')->with()->willReturn('pet_read'),
            Call::create('getPath')->with()->willReturn('/api/pets/{id}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new SymfonyRouter([$route]);
        $router->match($request);
    }

    public function testHostMatchFound(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('localhost'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_HOST => 'localhost',
            ]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('withAttributes')->with([])->willReturnSelf(),
        ]);

        $router = new SymfonyRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testHostMatchNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'The page "/" you are looking for could not be found.'
                .' Check the address bar to ensure your URL is spelled correctly.'
        );
        $this->expectExceptionCode(404);

        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('localhost'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getRequestTarget')->with()->willReturn('/'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_HOST => 'anotherhost',
            ]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new SymfonyRouter([$route]);
        $router->match($request);
    }

    public function testSchemeMatchFound(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('localhost'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_SCHEMES => ['https'],
            ]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('withAttributes')->with([])->willReturnSelf(),
        ]);

        $router = new SymfonyRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testSchemeMatchNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'The page "/" you are looking for could not be found.'
                .' Check the address bar to ensure your URL is spelled correctly.'
        );
        $this->expectExceptionCode(404);

        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('localhost'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getRequestTarget')->with()->willReturn('/'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_SCHEMES => ['http'],
            ]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new SymfonyRouter([$route]);
        $router->match($request);
    }

    public function testConditionMatchFound(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('localhost'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_CONDITION => "context.getHost() === 'localhost'",
            ]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('withAttributes')->with([])->willReturnSelf(),
        ]);

        $router = new SymfonyRouter([$route]);

        self::assertSame($route, $router->match($request));
    }

    public function testConditionMatchNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'The page "/" you are looking for could not be found.'
                .' Check the address bar to ensure your URL is spelled correctly.'
        );
        $this->expectExceptionCode(404);

        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('localhost'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getRequestTarget')->with()->willReturn('/'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_CONDITION => "context.getHost() !== 'localhost'",
            ]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new SymfonyRouter([$route]);
        $router->match($request);
    }

    public function testGenerateUri(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('http'),
            Call::create('getPort')->with()->willReturn(80),
            Call::create('getHost')->with()->willReturn('user:password@localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getScheme')->with()->willReturn('http'),
            Call::create('getPort')->with()->willReturn(10080),
            Call::create('getHost')->with()->willReturn('user:password@localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getScheme')->with()->willReturn('http'),
            Call::create('getPort')->with()->willReturn(null),
            Call::create('getHost')->with()->willReturn('user:password@localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('user:password@localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(10443),
            Call::create('getHost')->with()->willReturn('user:password@localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(null),
            Call::create('getHost')->with()->willReturn('user:password@localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_REQUIREMENTS => ['id' => '\d+', 'name' => '[a-z]+'],
                SymfonyRouter::PATH_DEFAULTS => ['name' => null],
            ]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}/{name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new SymfonyRouter([$route]);

        self::assertSame(
            'http://user:password@localhost/user/1',
            $router->generateUrl($request, 'user', ['id' => 1])
        );
        self::assertSame(
            'http://user:password@localhost:10080/user/1?key=value',
            $router->generateUrl($request, 'user', ['id' => 1], ['key' => 'value'])
        );
        self::assertSame(
            'http://user:password@localhost/user/1?key=value',
            $router->generateUrl($request, 'user', ['id' => 1], ['key' => 'value'])
        );
        self::assertSame(
            'https://user:password@localhost/user/1/sample',
            $router->generateUrl($request, 'user', ['id' => 1, 'name' => 'sample'])
        );
        self::assertSame(
            'https://user:password@localhost:10443/user/1/sample?key1=value1&key2=value2',
            $router->generateUrl(
                $request,
                'user',
                ['id' => 1, 'name' => 'sample'],
                ['key1' => 'value1', 'key2' => 'value2']
            )
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

    public function testGenerateUriWithMissingAttribute(): void
    {
        $this->expectException(MissingAttributeForPathGenerationException::class);
        $this->expectExceptionMessage(
            'Missing attribute "Some mandatory parameters are missing ("id") to generate a URL for route "user"."'
            .' while path generation for route: "user"'
        );
        $this->expectExceptionCode(3);

        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('user:password@localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_REQUIREMENTS => ['id' => '\d+', 'name' => '[a-z]+'],
                SymfonyRouter::PATH_DEFAULTS => ['name' => null],
            ]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}/{name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new SymfonyRouter([$route]);
        $router->generateUrl($request, 'user');
    }

    public function testGenerateUriWithNotMatchingAttribute(): void
    {
        $this->expectException(NotMatchingValueForPathGenerationException::class);
        $this->expectExceptionMessage(
            'Not matching value "" with pattern "" on attribute "Parameter "id" for route "user" must match "\d+"'
                .' ("a3bce0ca-2b7c-4fc6-8dad-ecdcc6907791" given) to generate a corresponding URL." while path'
                .' generation for route: "user"'
        );
        $this->expectExceptionCode(4);

        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('user:password@localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_REQUIREMENTS => ['id' => '\d+', 'name' => '[a-z]+'],
                SymfonyRouter::PATH_DEFAULTS => ['name' => null],
            ]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}/{name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new SymfonyRouter([$route]);
        $router->generateUrl($request, 'user', ['id' => 'a3bce0ca-2b7c-4fc6-8dad-ecdcc6907791']);
    }

    public function testGenerateUriWithBasePath(): void
    {
        /** @var UriInterface|MockObject $uri */
        $uri = $this->getMockByCalls(UriInterface::class, [
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('user:password@localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('user:password@localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('user:password@localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
            Call::create('getScheme')->with()->willReturn('https'),
            Call::create('getPort')->with()->willReturn(443),
            Call::create('getHost')->with()->willReturn('user:password@localhost'),
            Call::create('getPath')->with()->willReturn('/'),
            Call::create('getQuery')->with()->willReturn('?key=value'),
        ]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getUri')->with()->willReturn($uri),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_REQUIREMENTS => ['id' => '\d+', 'name' => '[a-z]+'],
                SymfonyRouter::PATH_DEFAULTS => ['name' => null],
            ]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}/{name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new SymfonyRouter([$route], null, '/path/to/directory');

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
        $this->expectException(MissingRouteByNameException::class);
        $this->expectExceptionMessage('Missing route: "user"');
        $this->expectExceptionCode(1);

        $router = new SymfonyRouter([]);
        $router->generatePath('user', ['id' => 1]);
    }

    public function testGeneratePath(): void
    {
        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_REQUIREMENTS => ['id' => '\d+', 'name' => '[a-z]+'],
                SymfonyRouter::PATH_DEFAULTS => ['name' => null],
            ]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}/{name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new SymfonyRouter([$route]);

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

    public function testGeneratePathWithMissingAttribute(): void
    {
        $this->expectException(MissingAttributeForPathGenerationException::class);
        $this->expectExceptionMessage(
            'Missing attribute "Some mandatory parameters are missing ("id") to generate a URL for route "user"."'
                .' while path generation for route: "user"'
        );

        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_REQUIREMENTS => ['id' => '\d+', 'name' => '[a-z]+'],
                SymfonyRouter::PATH_DEFAULTS => ['name' => null],
            ]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}/{name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new SymfonyRouter([$route]);
        $router->generatePath('user');
    }

    public function testGeneratePathWithBasePath(): void
    {
        /** @var RouteInterface|MockObject $route */
        $route = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPathOptions')->with()->willReturn([
                SymfonyRouter::PATH_REQUIREMENTS => ['id' => '\d+', 'name' => '[a-z]+'],
                SymfonyRouter::PATH_DEFAULTS => ['name' => null],
            ]),
            Call::create('getName')->with()->willReturn('user'),
            Call::create('getPath')->with()->willReturn('/user/{id}/{name}'),
            Call::create('getMethod')->with()->willReturn('GET'),
        ]);

        $router = new SymfonyRouter([$route], null, '/path/to/directory');

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

    public function testUseCache(): void
    {
        /** @var RouteInterface|MockObject $route1 */
        $route1 = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_create'),
            Call::create('getPathOptions')->with()->willReturn([]),
            Call::create('getName')->with()->willReturn('pet_create'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('POST'),
            Call::create('getName')->with()->willReturn('pet_create'),
        ]);

        /** @var RouteInterface|MockObject $route2 */
        $route2 = $this->getMockByCalls(RouteInterface::class, [
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPathOptions')->with()->willReturn([]),
            Call::create('getName')->with()->willReturn('pet_list'),
            Call::create('getPath')->with()->willReturn('/api/pets'),
            Call::create('getMethod')->with()->willReturn('GET'),
            Call::create('getName')->with()->willReturn('pet_list'),
        ]);

        $cacheFile = sys_get_temp_dir().'/symfony-'.uniqid().uniqid().'.php';

        new SymfonyRouter([$route1, $route2], $cacheFile);

        self::assertFileExists($cacheFile);

        new SymfonyRouter([$route1, $route2], $cacheFile);

        unlink($cacheFile);
    }
}
