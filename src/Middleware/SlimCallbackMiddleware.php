<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SlimCallbackMiddleware implements MiddlewareInterface
{
    private const string ATTRIBUTE_RESPONSE = 'response';

    /**
     * @var callable
     */
    private $slimCallable;

    public function __construct(callable $slimCallable, private readonly ResponseFactoryInterface $responseFactory)
    {
        $this->slimCallable = $slimCallable;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return ($this->slimCallable)(
            $request,
            $this->getResponse($request),
            static fn (ServerRequestInterface $request, ResponseInterface $response) => $handler->handle(
                $request->withAttribute(self::ATTRIBUTE_RESPONSE, $response)
            )
        );
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
