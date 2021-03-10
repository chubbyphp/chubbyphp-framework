<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\RequestHandler;

use Chubbyphp\Framework\Debug;
use Chubbyphp\Framework\DebugInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SlimLazyRequestHandler implements DebugInterface, RequestHandlerInterface
{
    private ContainerInterface $container;

    private string $id;

    private ResponseFactoryInterface $responseFactory;

    public function __construct(ContainerInterface $container, string $id, ResponseFactoryInterface $responseFactory)
    {
        $this->container = $container;
        $this->id = $id;
        $this->responseFactory = $responseFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestHandler = $this->container->get($this->id);
        if (!is_callable($requestHandler)) {
            throw new \TypeError(
                sprintf(
                    '%s() expects service with id "%s" to be %s, %s given',
                    __METHOD__,
                    $this->id,
                    'callable',
                    is_object($requestHandler) ? get_class($requestHandler) : gettype($requestHandler)
                )
            );
        }

        return (new SlimCallbackRequestHandler($requestHandler, $this->responseFactory))->handle($request);
    }

    public function debug(): array
    {
        return Debug::debug([
            'class' => self::class,
            'container' => $this->container,
            'id' => $this->id,
            'responseFactory' => $this->responseFactory,
        ]);
    }
}
