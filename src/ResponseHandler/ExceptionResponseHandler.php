<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\ResponseHandler;

use Chubbyphp\Framework\Router\RouteException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @copyright Parts of this code are copied by the Slim Framework
 *
 * @see https://github.com/slimphp/Slim/blob/3.x/Slim/Handlers/Error.php
 */
final class ExceptionResponseHandler implements ExceptionResponseHandlerInterface
{
    const ERROR_HTML = <<<'EOT'
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

            strong {
                display: inline-block;
                width: 65px;
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
     * @var bool
     */
    private $debug;

    /**
     * @param ResponseFactoryInterface $responseFactory
     * @param bool                     $debug
     */
    public function __construct(ResponseFactoryInterface $responseFactory, bool $debug = false)
    {
        $this->responseFactory = $responseFactory;
        $this->debug = $debug;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RouteException         $routeException
     *
     * @return ResponseInterface
     */
    public function createRouteExceptionResponse(
        ServerRequestInterface $request,
        RouteException $routeException
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse($routeException->getCode());
        $response->getBody()->write(sprintf(
            self::ERROR_HTML,
            $routeException->getTitle(),
            $routeException->getTitle(),
            '<p>'.$routeException->getMessage().'</p>'
        ));

        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param \Throwable             $exception
     *
     * @return ResponseInterface
     */
    public function createExceptionResponse(ServerRequestInterface $request, \Throwable $exception): ResponseInterface
    {
        if ($this->debug) {
            $html = '<p>The application could not run because of the following error:</p>';
            $html .= '<h2>Details</h2>';
            $html .= $this->renderException($exception);

            while ($exception = $exception->getPrevious()) {
                $html .= '<h2>Previous exception</h2>';
                $html .= $this->renderException($exception);
            }
        } else {
            $html = '<p>A website error has occurred. Sorry for the temporary inconvenience.</p>';
        }

        $response = $this->responseFactory->createResponse(500);
        $response->getBody()->write(sprintf(
            self::ERROR_HTML,
            'Application Error',
            'Application Error',
            $html
        ));

        return $response;
    }

    /**
     * @param \Throwable $exception
     *
     * @return string
     */
    private function renderException(\Throwable $exception): string
    {
        $html = sprintf('<div><strong>Type:</strong> %s</div>', get_class($exception));
        $html .= sprintf('<div><strong>Code:</strong> %s</div>', $exception->getCode());
        $html .= sprintf('<div><strong>Message:</strong> %s</div>', htmlentities($exception->getMessage()));
        $html .= sprintf('<div><strong>File:</strong> %s</div>', $exception->getFile());
        $html .= sprintf('<div><strong>Line:</strong> %s</div>', $exception->getLine());
        $html .= '<h2>Trace</h2>';
        $html .= sprintf('<pre>%s</pre>', htmlentities($exception->getTraceAsString()));

        return $html;
    }
}
