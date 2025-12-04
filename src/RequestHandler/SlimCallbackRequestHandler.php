<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\RequestHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SlimCallbackRequestHandler implements RequestHandlerInterface
{
    private const string ATTRIBUTE_RESPONSE = 'response';

    /**
     * @var callable
     */
    private $slimCallable;

    public function __construct(callable $slimCallable, private ResponseFactoryInterface $responseFactory)
    {
        $this->slimCallable = $slimCallable;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->slimCallable)($request, $this->getResponse($request), $request->getAttributes());
    }

    private function getResponse(ServerRequestInterface $request): ResponseInterface
    {
        /** @var null|ResponseInterface $response */
        $response = $request->getAttribute(self::ATTRIBUTE_RESPONSE);

        if (null !== $response) {
            return $response;
        }

        return $this->responseFactory->createResponse();
    }
}
