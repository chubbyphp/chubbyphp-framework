<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\ResponseHandler\ExceptionResponseHandler;
use Chubbyphp\Framework\Router\FastRoute\RouteDispatcher;
use Chubbyphp\Framework\Router\RouteCollection;
use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;
use Chubbyphp\Framework\Router\FastRoute\UrlGenerator;

$loader = require __DIR__.'/vendor/autoload.php';

$responseFactory = new ResponseFactory();

$requestHandler = new class($responseFactory) implements RequestHandlerInterface
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

    /**
     * @param ServerRequestInterface $request
        *
        * @return ResponseInterface
        */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(['timestamp' => time()]));

        return $response;
    }
};

$routeCollection = (new RouteCollection())
    ->route('/user/{id:\d+}[/{name}]', RouteInterface::GET, 'index', $requestHandler);

$request = ServerRequestFactory::fromGlobals();

$urlGenerator = new UrlGenerator($routeCollection);

echo $urlGenerator->requestTarget('index', ['id' => 1, 'name' => 'sample', 'key' => 'value']) . PHP_EOL;
echo $urlGenerator->requestTarget('index', ['id' => 1, 'key' => 'value']) . PHP_EOL;
