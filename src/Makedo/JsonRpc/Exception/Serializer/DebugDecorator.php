<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Exception\Serializer;

use Makedo\JsonRpc\Exception\JsonRpcError;

class DebugDecorator implements ErrorSerializer
{
    private ErrorSerializer $serializer;

    public function __construct(ErrorSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serialize(JsonRpcError $e): array
    {
        $json = $this->serializer->serialize($e);

        $json['data'] = $json['data'] ?? [];
        $json['data']['debug'] = [
            'debugMessage'    => $e->getDebugMessage(),
            'previousMessage' => $e->getPrevious() ? $e->getPrevious()->getMessage() : null,
            'trace'           => $e->getTrace()
        ];

        return $json;
    }
}
