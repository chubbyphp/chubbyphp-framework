<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

use Chubbyphp\Framework\Router\RouterException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @copyright Parts of this code are copied from the Slim Framework
 *
 * @see https://github.com/slimphp/Slim/blob/3.x/Slim/Handlers/Error.php
 */
final class ExceptionHandler implements ExceptionHandlerInterface
{
    public const EXCEPTION_HTML = <<<'EOT'
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
        <h1>%s</h1>
        %s
    </body>
</html>
EOT;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        bool $debug = false,
        ?LoggerInterface $logger = null
    ) {
        $this->responseFactory = $responseFactory;
        $this->debug = $debug;
        $this->logger = $logger ?? new NullLogger();
    }

    public function createRouterExceptionResponse(
        ServerRequestInterface $request,
        RouterException $routerException
    ): ResponseInterface {
        $this->logger->info('Route exception', [
            'title' => $routerException->getTitle(),
            'message' => $routerException->getMessage(),
            'code' => $routerException->getCode(),
        ]);

        $response = $this->responseFactory->createResponse($routerException->getCode());
        $response = $response->withHeader('Content-Type', 'text/html');
        $response->getBody()->write(sprintf(
            self::EXCEPTION_HTML,
            $routerException->getTitle(),
            $routerException->getTitle(),
            '<p>'.$routerException->getMessage().'</p>'
        ));

        return $response;
    }

    public function createExceptionResponse(ServerRequestInterface $request, \Throwable $exception): ResponseInterface
    {
        $exceptionsData = ExceptionHelper::toArray($exception);

        $this->logger->error('Exception', ['exceptions' => $exceptionsData]);

        $html = '<p>A website error has occurred. Sorry for the temporary inconvenience.</p>';

        if ($this->debug) {
            $html .= $this->addDebugToHtml($exceptionsData);
        }

        $response = $this->responseFactory->createResponse(500);
        $response = $response->withHeader('Content-Type', 'text/html');
        $response->getBody()->write(sprintf(
            self::EXCEPTION_HTML,
            'Application Error',
            'Application Error',
            $html
        ));

        return $response;
    }

    /**
     * @param array<array<string, string>> $exceptionsData
     */
    private function addDebugToHtml(array $exceptionsData): string
    {
        $html = '<h2>Details</h2>';
        foreach ($exceptionsData as $exceptionData) {
            $html .= '<div class="block">';
            foreach ($exceptionData as $key => $value) {
                $html .= sprintf(
                    '<div><div class="key"><strong>%s</strong></div><div class="value">%s</div></div>',
                    ucfirst($key),
                    nl2br((string) $value)
                );
            }

            $html .= '</div>';
        }

        return $html;
    }
}
