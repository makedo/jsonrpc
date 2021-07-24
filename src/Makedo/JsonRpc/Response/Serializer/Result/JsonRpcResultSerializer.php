<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Response\Serializer\Result;

class JsonRpcResultSerializer implements ResultSerializer
{
    public function serialize($result)
    {
        return $result;
    }
}
