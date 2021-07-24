<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Handler\Request;

use Makedo\JsonRpc\Handler\Request\RequestHandler;
use Makedo\JsonRpc\Request;
use Makedo\JsonRpc\Response;
use Makedo\JsonRpc\Response\EmptyResponse;

class HandlerDecorator implements RequestHandler
{
    private RequestHandler $requestHandler;

    public function __construct(RequestHandler $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $jsonRpcRequest): Response
    {
        try {
            $jsonRpcResponse = $this->requestHandler->handle($jsonRpcRequest);
        } catch (\Throwable $e) {
            $jsonRpcResponse = Response\JsonRpcResponse::error($e);
        }

        $jsonRpcResponse->setId($jsonRpcRequest->getId());
        if ($jsonRpcRequest->isNotification()) {
            return new EmptyResponse($jsonRpcResponse);
        }

        return $jsonRpcResponse;
    }
}
