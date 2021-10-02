<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Emitter
{
    final class TestHeader
    {
        private static array $headers = [];

        /**
         * @param int $http_response_code
         */
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

    /**
     * @param int $http_response_code
     */
    function header(string $header, bool $replace = true, ?int $http_response_code = null): void
    {
        TestHeader::add($header, $replace, $http_response_code);
    }
}

namespace Chubbyphp\Tests\Framework\Unit\Emitter
{
    use Chubbyphp\Framework\Emitter\Emitter;
    use Chubbyphp\Framework\Emitter\TestHeader;
    use Chubbyphp\Mock\Call;
    use Chubbyphp\Mock\MockByCallsTrait;
    use PHPUnit\Framework\MockObject\MockObject;
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
        use MockByCallsTrait;

        public function testEmit(): void
        {
            /** @var MockObject|StreamInterface $responseBody */
            $responseBody = $this->getMockByCalls(StreamInterface::class, [
                Call::create('isSeekable')->with()->willReturn(true),
                Call::create('rewind')->with(),
                Call::create('eof')->with()->willReturn(false),
                Call::create('read')->with(256)->willReturn('sample body'),
                Call::create('eof')->with()->willReturn(true),
            ]);

            /** @var MockObject|ResponseInterface $response */
            $response = $this->getMockByCalls(ResponseInterface::class, [
                Call::create('getStatusCode')->with()->willReturn(200),
                Call::create('getProtocolVersion')->with()->willReturn('1.1'),
                Call::create('getReasonPhrase')->with()->willReturn('OK'),
                Call::create('getHeaders')->with()->willReturn(['X-Name' => ['value1', 'value2']]),
                Call::create('getBody')->with()->willReturn($responseBody),
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
