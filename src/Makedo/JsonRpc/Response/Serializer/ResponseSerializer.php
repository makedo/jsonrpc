<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Response\Serializer;

use Makedo\JsonRpc\Response;

interface ResponseSerializer
{
    public function serialize(Response $response): ?array;
}
