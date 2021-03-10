<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Middleware;

use Chubbyphp\Framework\Debug;
use Chubbyphp\Framework\DebugInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SlimCallbackMiddleware implements DebugInterface, MiddlewareInterface
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

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return ($this->slimCallable)(
            $request,
            $this->getResponse($request),
            static fn (ServerRequestInterface $request, ResponseInterface $response) => $handler->handle($request->withAttribute(self::ATTRIBUTE_RESPONSE, $response))
        );
    }

    public function debug(): array
    {
        return Debug::debug([
            'class' => self::class,
            'slimCallable' => $this->slimCallable,
            'responseFactory' => $this->responseFactory,
        ]);
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
