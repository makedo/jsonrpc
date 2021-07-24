<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Json;

use Makedo\JsonRpc\Exception\JsonRpcError;

class DefaultEncoder implements Encoder
{
    private int $options;
    private int $depth;

    public function __construct(int $options = 0, int $depth = 512)
    {
        $this->options = $options | JSON_THROW_ON_ERROR;
        $this->depth = $depth;
    }

    public function encode($data): string
    {
        if (null === $data) {
            return '';
        }

        try {
            return json_encode(
                $data,
                $this->options,
                $this->depth
            );
        } catch (\Throwable $e) {
            throw JsonRpcError::internalError('An error occurred during encoding to json', $e);
        }
    }
}
