<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ExceptionMiddleware implements MiddlewareInterface
{
    private const HTML = <<<'EOT'
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>%s</title>
                <style>
                    body {
                        margin: 0;
                        padding: 30px;
                        font: 12px/1.5 Helvetica, Arial, Verdana, sans-serif;
                    }

                    h1 {
                        margin: 0;
                        font-size: 48px;
                        font-weight: normal;
                        line-height: 48px;
                    }

                    .block {
                        margin-bottom: 20px;
                    }

                    .key {
                        width: 100px;
                        display: inline-flex;
                    }

                    .value {
                        display: inline-flex;
                    }
                </style>
            </head>
            <body>
                %s
            </body>
        </html>
        EOT;

    private LoggerInterface $logger;

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private bool $debug = false,
        ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    private function handleException(\Throwable $exception): ResponseInterface
    {
        $exceptionsData = $this->toExceptionArray($exception);

        $this->logger->error('Exception', ['exceptions' => $exceptionsData]);

        $body = '<h1>Application Error</h1>'
            .'<p>A website error has occurred. Sorry for the temporary inconvenience.</p>';

        if ($this->debug) {
            $body .= $this->addDebugToBody($exceptionsData);
        }

        $response = $this->responseFactory->createResponse(500);
        $response = $response->withHeader('Content-Type', 'text/html');
        $response->getBody()->write(sprintf(
            self::HTML,
            'Application Error',
            $body
        ));

        return $response;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function toExceptionArray(\Throwable $exception): array
    {
        $exceptions = [];
        do {
            $exceptions[] = [
                'class' => $exception::class,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        } while ($exception = $exception->getPrevious());

        return $exceptions;
    }

    /**
     * @param array<array<string, string>> $exceptionsData
     */
    private function addDebugToBody(array $exceptionsData): string
    {
        $body = '<h2>Details</h2>';
        foreach ($exceptionsData as $exceptionData) {
            $body .= '<div class="block">';
            foreach ($exceptionData as $key => $value) {
                $body .= sprintf(
                    '<div><div class="key"><strong>%s</strong></div><div class="value">%s</div></div>',
                    ucfirst($key),
                    nl2br((string) $value)
                );
            }

            $body .= '</div>';
        }

        return $body;
    }
}
