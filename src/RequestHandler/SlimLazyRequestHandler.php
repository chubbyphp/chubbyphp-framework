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
    public function __construct(private ContainerInterface $container, private string $id, private ResponseFactoryInterface $responseFactory)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var callable $requestHandler */
        $requestHandler = $this->container->get($this->id);

        return (new SlimCallbackRequestHandler($requestHandler, $this->responseFactory))->handle($request);
    }
}
