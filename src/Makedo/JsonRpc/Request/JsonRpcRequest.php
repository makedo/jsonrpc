<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Request;

use Makedo\JsonRpc\Request;

class JsonRpcRequest implements Request
{
    private string $jsonrpc;
    private string $method;
    private array $params;
    private string|int|null $id;

    /**
     * @param string $jsonrpc
     * @param string $method
     * @param array $params
     * @param string|int|null $id
     */
    public function __construct(
        string $jsonrpc,
        string $method,
        array $params,
        string|int|null $id = null
    ) {
        $this->jsonrpc = $jsonrpc;
        $this->method = $method;
        $this->params = $params;
        $this->id = $id;
    }

    public function getJsonrpc(): string
    {
        return $this->jsonrpc;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function isNotification(): bool
    {
        return is_null($this->id);
    }
}
