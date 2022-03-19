<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\RequestHandler;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyRequestHandler implements RequestHandlerInterface
{
    public function __construct(private ContainerInterface $container, private string $id)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $this->container->get($this->id);

        return $requestHandler->handle($request);
    }
}
