<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Exception;

class JsonRpcError extends \Exception
{
    protected string $debugMessage = '';
    protected ?array $data = null;

    public static function parseError(string $debugMessage, \Throwable $previous = null): JsonRpcError
    {
        $e = new self('Parse error', -32700, $previous);
        $e->debugMessage = $debugMessage;

        return $e;
    }

    public static function invalidRequest(string $debugMessage, \Throwable $previous = null): JsonRpcError
    {
        $e = new self('Invalid Request', -32600, $previous);
        $e->debugMessage = $debugMessage;

        return $e;
    }

    public static function methodNotFound(string $debugMessage, \Throwable $previous = null): JsonRpcError
    {
        $e = new self('Method not found', -32601, $previous);
        $e->debugMessage = $debugMessage;

        return $e;
    }

    public static function invalidParams(string $debugMessage, ?array $data = null, \Throwable $previous = null): JsonRpcError
    {
        $e = new self('Invalid params', -32602, $previous);
        $e->debugMessage = $debugMessage;
        $e->data = $data;

        return $e;
    }

    public static function internalError(string $debugMessage, \Throwable $previous = null): JsonRpcError
    {
        $e = new static('Internal error', -32603, $previous);
        $e->debugMessage = $debugMessage;

        return $e;
    }

    public function serverError(int $code, string $debugMessage, \Throwable $previous = null): JsonRpcError
    {
        $e = new self('Server error', $code, $previous);
        $e->debugMessage = $debugMessage;

        return $e;
    }

    public function getDebugMessage(): string
    {
        return $this->debugMessage;
    }

    public function getData(): ?array
    {
        return $this->data;
    }
}
