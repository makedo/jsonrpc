<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Json;

use Makedo\JsonRpc\Exception\JsonRpcError;

class DefaultDecoder implements Decoder
{
    private int $options;
    private int $depth;

    public function __construct(int $options = 0, int $depth = 512)
    {
        $this->options = $options | JSON_THROW_ON_ERROR;
        $this->depth = $depth;
    }

    public function decode(string $json): array
    {
        if (empty($json)) {
            throw JsonRpcError::parseError('Empty json string provided');
        }

        try {
            $decoded = json_decode(
                $json,
                true,
                $this->depth,
                $this->options
            );
        } catch (\Throwable $e) {
            throw JsonRpcError::parseError('Invalid json string provided', $e);
        }

        if (empty($decoded)) {
            throw JsonRpcError::invalidRequest('Decoded json structure is empty');
        }

        return $decoded;
    }
}
