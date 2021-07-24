<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Exception\Serializer;

use Makedo\JsonRpc\Exception\JsonRpcError;

interface ErrorSerializer
{
    public function serialize(JsonRpcError $e): array;
}
