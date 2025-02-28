<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Emitter
{
    final class TestHeader
    {
        private static array $headers = [];

        public static function add(string $header, bool $replace = true, ?int $http_response_code = null): void
        {
            self::$headers[] = [
                'header' => $header,
                'replace' => $replace,
                'http_response_code' => $http_response_code,
            ];
        }

        public static function all(): array
        {
            return self::$headers;
        }

        public static function reset(): void
        {
            self::$headers = [];
        }
    }

    function header(string $header, bool $replace = true, ?int $http_response_code = null): void
    {
        TestHeader::add($header, $replace, $http_response_code);
    }
}

namespace Chubbyphp\Tests\Framework\Unit\Emitter
{
    use Chubbyphp\Framework\Emitter\Emitter;
    use Chubbyphp\Framework\Emitter\TestHeader;
    use Chubbyphp\Mock\MockMethod\WithCallback;
    use Chubbyphp\Mock\MockMethod\WithReturn;
    use Chubbyphp\Mock\MockObjectBuilder;
    use PHPUnit\Framework\TestCase;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\StreamInterface;

    /**
     * @covers \Chubbyphp\Framework\Emitter\Emitter
     *
     * @internal
     */
    final class EmitterTest extends TestCase
    {
        public function testEmit(): void
        {
            $builder = new MockObjectBuilder();

            /** @var StreamInterface $responseBody */
            $responseBody = $builder->create(StreamInterface::class, [
                new WithReturn('isSeekable', [], true),
                new WithCallback('rewind', static fn () => null),
                new WithReturn('eof', [], false),
                new WithReturn('read', [256], 'sample body'),
                new WithReturn('eof', [], true),
            ]);

            /** @var ResponseInterface $response */
            $response = $builder->create(ResponseInterface::class, [
                new WithReturn('getStatusCode', [], 200),
                new WithReturn('getProtocolVersion', [], '1.1'),
                new WithReturn('getReasonPhrase', [], 'OK'),
                new WithReturn('getHeaders', [], ['X-Name' => ['value1', 'value2']]),
                new WithReturn('getBody', [], $responseBody),
            ]);

            $emitter = new Emitter();

            TestHeader::reset();

            ob_start();

            $emitter->emit($response);

            self::assertEquals([
                [
                    'header' => 'HTTP/1.1 200 OK',
                    'replace' => true,
                    'http_response_code' => 200,
                ],
                [
                    'header' => 'X-Name: value1',
                    'replace' => false,
                    'http_response_code' => null,
                ],
                [
                    'header' => 'X-Name: value2',
                    'replace' => false,
                    'http_response_code' => null,
                ],
            ], TestHeader::all());

            self::assertSame('sample body', ob_get_clean());
        }
    }
}
