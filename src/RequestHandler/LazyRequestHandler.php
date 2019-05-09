<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\RequestHandler;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $id;

    /**
     * @param ContainerInterface $container
     * @param string             $id
     */
    public function __construct(ContainerInterface $container, string $id)
    {
        $this->container = $container;
        $this->id = $id;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestHandler = $this->container->get($this->id);
        if (!$requestHandler instanceof RequestHandlerInterface) {
            throw new \TypeError(
                sprintf(
                    '%s() expects service with id "%s" to be %s, %s given',
                    __METHOD__,
                    $this->id,
                    RequestHandlerInterface::class,
                    is_object($requestHandler) ? get_class($requestHandler) : gettype($requestHandler)
                )
            );
        }

        return $requestHandler->handle($request);
    }
}
