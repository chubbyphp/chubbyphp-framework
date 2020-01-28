<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Emitter;

use Psr\Http\Message\ResponseInterface;

final class Emitter implements EmitterInterface
{
    public function emit(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        header(sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $statusCode,
            $response->getReasonPhrase()
        ), true, $statusCode);

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        $body = $response->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        while (!$body->eof()) {
            echo $body->read(256);
        }
    }
}
