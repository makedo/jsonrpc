<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Request\Factory;

use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Request;
use Makedo\JsonRpc\Request\JsonRpcRequest;

interface RequestFactory
{
    /**
     * @param array $requestData
     * @return Request
     * @throws JsonRpcError
     */
    public function createRequest(array $requestData): Request;
}
