<?php

namespace Makedo\JsonRpc\Exception\Serializer;

use Makedo\JsonRpc\Exception\JsonRpcError;

class JsonRpcErrorSerializer implements ErrorSerializer
{
    public static function create(bool $debug = false): ErrorSerializer
    {
        return $debug ? new DebugDecorator(new static()) : new static();
    }

    public function serialize(JsonRpcError $e): array
    {
        $json = [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
        ];

        $data = $e->getData();
        if (null !== $data) {
            $json['data'] = $data;
        }

        return $json;
    }
}
