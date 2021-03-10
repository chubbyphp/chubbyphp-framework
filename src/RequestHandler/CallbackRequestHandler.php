<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\RequestHandler;

use Chubbyphp\Framework\Debug;
use Chubbyphp\Framework\DebugInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CallbackRequestHandler implements DebugInterface, RequestHandlerInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->callback)($request);
    }

    public function debug(): array
    {
        return Debug::debug([
            'class' => self::class,
            'callback' => $this->callback,
        ]);
    }
}
