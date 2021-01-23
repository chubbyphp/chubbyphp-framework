<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\RequestHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SlimCallbackRequestHandler implements RequestHandlerInterface
{
    private const ATTRIBUTE_RESPONSE = 'response';

    /**
     * @var callable
     */
    private $slimCallable;

    private ResponseFactoryInterface $responseFactory;

    public function __construct(callable $slimCallable, ResponseFactoryInterface $responseFactory)
    {
        $this->slimCallable = $slimCallable;
        $this->responseFactory = $responseFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->slimCallable)($request, $this->getResponse($request), $request->getAttributes());
    }

    private function getResponse(ServerRequestInterface $request): ResponseInterface
    {
        /** @var ResponseInterface|null $response */
        $response = $request->getAttribute(self::ATTRIBUTE_RESPONSE);

        if (null !== $response) {
            return $response;
        }

        return $this->responseFactory->createResponse();
    }
}
