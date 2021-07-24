<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Json;

use Makedo\JsonRpc\Exception\JsonRpcError;

interface Decoder
{
    /**
     * @param string $json
     * @throws JsonRpcError
     * @return array
     */
    public function decode(string $json): array;
}
