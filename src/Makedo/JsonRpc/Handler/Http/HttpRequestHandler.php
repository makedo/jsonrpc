<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Handler\Http;

use Makedo\JsonRpc\Handler\Request\RequestHandler;
use Makedo\JsonRpc\Handler\Stream\JsonGeneratorStreamHandler;
use Makedo\JsonRpc\Handler\Stream\StreamHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpRequestHandler implements RequestHandlerInterface
{
    private StreamHandler $jsonRpcStreamHandler;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        StreamHandler $jsonRpcStreamHandler,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->jsonRpcStreamHandler = $jsonRpcStreamHandler;
        $this->responseFactory = $responseFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestStream = $request->getBody();

        $responseStream = $this->jsonRpcStreamHandler->handle($requestStream);

        return $this->responseFactory
            ->createResponse()
            ->withHeader('Content-Type', 'application/json')
            ->withBody($responseStream)
        ;
    }
}
