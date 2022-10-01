<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\RequestHandler;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SlimLazyRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private ContainerInterface $container,
        private string $id,
        private ResponseFactoryInterface $responseFactory
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestHandler = $this->container->get($this->id);
        if (!\is_callable($requestHandler)) {
            throw new \TypeError(
                sprintf(
                    '%s() expects service with id "%s" to be %s, %s given',
                    __METHOD__,
                    $this->id,
                    'callable',
                    get_debug_type($requestHandler)
                )
            );
        }

        return (new SlimCallbackRequestHandler($requestHandler, $this->responseFactory))->handle($request);
    }
}
