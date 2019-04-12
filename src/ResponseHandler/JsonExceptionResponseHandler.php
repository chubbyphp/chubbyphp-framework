<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\ResponseHandler;

use Chubbyphp\Framework\ExceptionHelper;
use Chubbyphp\Framework\Router\RouteDispatcherException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class JsonExceptionResponseHandler implements ExceptionResponseHandlerInterface
{
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
     * @param ServerRequestInterface   $request
     * @param RouteDispatcherException $routeException
     *
     * @return ResponseInterface
     */
    public function createRouteDispatcherExceptionResponse(
        ServerRequestInterface $request,
        RouteDispatcherException $routeException
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse($routeException->getCode());
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode([
            'type' => $routeException->getType(),
            'title' => $routeException->getTitle(),
            'detail' => $routeException->getMessage(),
        ]));

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
        $response = $this->responseFactory->createResponse(500);
        $response = $response->withHeader('Content-Type', 'application/json');

        $data = [
            'type' => 'https://tools.ietf.org/html/rfc7231#section-6.6.1',
            'title' => 'Internal Server Error',
        ];

        if ($this->debug) {
            $data['exceptions'] = ExceptionHelper::toArray($exception);
        }

        $response->getBody()->write(json_encode($data));

        return $response;
    }
}
