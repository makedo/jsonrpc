<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Response\Serializer;

use Makedo\JsonRpc\Exception\Serializer\ErrorSerializer;
use Makedo\JsonRpc\Response;
use Makedo\JsonRpc\Response\Serializer\Result\ResultSerializer;
use Makedo\JsonRpc\Version;

class JsonRpcResponseSerializer implements ResponseSerializer
{
    private ErrorSerializer $errorSerializer;
    private ResultSerializer $resultSerializer;

    public function __construct(ErrorSerializer $errorSerializer, ResultSerializer $resultSerializer)
    {
        $this->errorSerializer = $errorSerializer;
        $this->resultSerializer = $resultSerializer;
    }

    public function serialize(Response $response): ?array
    {
        if ($response instanceof Response\EmptyResponse) {
            return null;
        }

        $json = [
            'jsonrpc' => Version::number(),
        ];

        if ($response->hasError()) {
            $json['error'] = $this->errorSerializer->serialize($response->getError());
        } else {
            $json['result'] = $this->resultSerializer->serialize($response->getResult());
        }

        $json['id'] = $response->getId();

        return $json;
    }
}
