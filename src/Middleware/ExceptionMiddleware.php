<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Chubbyphp\HttpException\HttpException;
use Chubbyphp\HttpException\HttpExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ExceptionMiddleware implements MiddlewareInterface
{
    private const string HTML = <<<'EOT'
        <!DOCTYPE html>
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>__TITLE__</title>
                <style>
                    html {
                        font-family: Helvetica, Arial, Verdana, sans-serif;
                        line-height: 1.5;
                        tab-size: 4;
                    }

                    body {
                        margin: 0;
                    }

                    * {
                        border-width: 0;
                        border-style: solid;
                    }

                    .container {
                        width: 100%
                    }

                    .mx-auto {
                        margin-left: auto;
                        margin-right: auto;
                    }

                    .mt-12 {
                        margin-top: 3rem;
                    }

                    .mb-12 {
                        margin-bottom: 3rem;
                    }

                    .text-gray-400 {
                        --tw-text-opacity: 1;
                        color: rgba(156, 163, 175, var(--tw-text-opacity));
                    }

                    .text-5xl {
                        font-size: 3rem;
                        line-height: 1;
                    }

                    .text-right {
                        text-align: right;
                    }

                    .tracking-tighter {
                        letter-spacing: -.05em;
                    }

                    .flex {
                        display: flex;
                    }

                    .flex-row {
                        flex-direction: row;
                    }

                    .basis-2\/12 {
                        flex-basis: 16.666667%;
                    }

                    .basis-10\/12 {
                        flex-basis: 83.333333%;
                    }

                    .space-x-8>:not([hidden])~:not([hidden]) {
                        --tw-space-x-reverse: 0;
                        margin-right: calc(2rem * var(--tw-space-x-reverse));
                        margin-left: calc(2rem * calc(1 - var(--tw-space-x-reverse)))
                    }

                    .gap-x-4 {
                        column-gap: 1rem;
                    }

                    .gap-y-1\.5 {
                        row-gap: 0.375rem;
                    }

                    .grid-cols-1 {
                        grid-template-columns: repeat(1, minmax(0, 1fr));
                    }

                    .grid {
                        display: grid;
                    }

                    @media (min-width:640px) {
                        .container {
                            max-width: 640px
                        }
                    }

                    @media (min-width:768px) {
                        .container {
                            max-width: 768px
                        }

                        .md\:grid-cols-8 {
                            grid-template-columns: repeat(8, minmax(0, 1fr));
                        }

                        .md\:col-span-7 {
                            grid-column: span 7/span 7
                        }
                    }

                    @media (min-width:1024px) {
                        .container {
                            max-width: 1024px
                        }
                    }

                    @media (min-width:1280px) {
                        .container {
                            max-width: 1280px
                        }
                    }

                    @media (min-width:1536px) {
                        .container {
                            max-width: 1536px
                        }
                    }
                </style>
            </head>
            <body>
                <div class="container mx-auto tracking-tighter mt-12">
                    <div class="flex flex-row space-x-8">
                        <div class="basis-1/12 text-5xl text-gray-400 text-right">__STATUS__</div>
                        <div class="basis-11/12">
                            <span class="text-5xl">__TITLE__</span>__BODY__
                        </div>
                    </div>
                </div>
            </body>
        </html>
        EOT;

    private readonly LoggerInterface $logger;

    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly bool $debug = false,
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
        $httpException = $this->exceptionToHttpException($exception);

        $data = $httpException->jsonSerialize();

        $logMethod = $data['status'] < 500 ? 'info' : 'error';

        $exceptions = $this->toExceptionsArray($httpException);

        $this->logger->{$logMethod}('Http Exception', [
            'data' => $data,
            'exceptions' => $exceptions,
        ]);

        $lines = [
            ...(isset($data['detail']) ? ['<p>'.$data['detail'].'</p>'] : []),
            ...(isset($data['instance']) ? ['<p>'.$data['instance'].'</p>'] : []),
            ...($this->debug ? [$this->addDebugToBody($exceptions)] : []),
        ];

        $response = $this->responseFactory->createResponse($data['status']);
        $response = $response->withHeader('Content-Type', 'text/html');
        $response->getBody()->write(
            str_replace(
                '__STATUS__',
                (string) $data['status'],
                str_replace(
                    '__TITLE__',
                    $data['title'],
                    str_replace(
                        '__BODY__',
                        implode('', $lines),
                        self::HTML
                    )
                )
            )
        );

        return $response;
    }

    private function exceptionToHttpException(\Throwable $exception): HttpExceptionInterface
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception;
        }

        return HttpException::createInternalServerError([
            'detail' => 'A website error has occurred. Sorry for the temporary inconvenience.',
        ], $exception);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function toExceptionsArray(\Throwable $exception): array
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
        $body = '<div class="mt-12">';
        foreach ($exceptionsData as $exceptionData) {
            $body .= '<div class="mb-12 grid grid-cols-1 md:grid-cols-8 gap-4">';
            foreach ($exceptionData as $key => $value) {
                $body .= \sprintf(
                    '<div><strong>%s</strong></div><div class="md:col-span-7">%s</div>',
                    ucfirst($key),
                    nl2br((string) $value)
                );
            }

            $body .= '</div>';
        }
        $body .= '</div>';

        return $body;
    }
}
