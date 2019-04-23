<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\ResponseHandler\HtmlExceptionResponseHandler;
use Chubbyphp\Framework\Router\AuraRouter\RouteDispatcher;
use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface as PsrRequestHandlerInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

$responseFactory = new ResponseFactory();

$group = Group::create('')
    ->route(
        Route::get(
            '/hello/{name}',
            'hello',
            new class($responseFactory) implements PsrRequestHandlerInterface
            {
                /**
                 * @var ResponseFactoryInterface
                 */
                private $responseFactory;

                /**
                 * @param ResponseFactoryInterface $responseFactory
                 */
                public function __construct(ResponseFactoryInterface $responseFactory)
                {
                    $this->responseFactory = $responseFactory;
                }

                public function handle(ServerRequestInterface $request): ResponseInterface
                {
                    $name = $request->getAttribute('name');
                    $response = $this->responseFactory->createResponse();
                    $response->getBody()->write(sprintf('Hello, %s', $name));

                    return $response;
                }
            }
        )->pathOptions(['tokens' => ['name' => '[a-z]+']])
    );

$app = new Application(
    new RouteDispatcher($group),
    new MiddlewareDispatcher(),
    new HtmlExceptionResponseHandler($responseFactory)
);

$app->run(ServerRequestFactory::fromGlobals());
