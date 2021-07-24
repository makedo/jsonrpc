<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Handler\Request;

use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Handler\Request\RequestHandler;
use Makedo\JsonRpc\Request;
use Makedo\JsonRpc\Response;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContainerHandler implements RequestHandler
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handle(Request $request): Response
    {
        $method = $request->getMethod();

        try {
            $handler = $this->container->get($method);
        } catch (NotFoundExceptionInterface $e) {
            throw JsonRpcError::methodNotFound(sprintf(
                'Handler %s not found in container',
                $method
            ), $e);
        }

        return $this->handleRequest($handler, $request);
    }

    public function handleRequest(RequestHandler $handler, Request $request): Response
    {
        return $handler->handle($request);
    }
}
