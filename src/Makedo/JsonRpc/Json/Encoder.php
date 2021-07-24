<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Json;

use Makedo\JsonRpc\Exception\JsonRpcError;

interface Encoder
{
    /**
     * @param null|object|array $data
     * @throws JsonRpcError
     * @return string
     */
    public function encode(array|object|null $data) : string;
}
