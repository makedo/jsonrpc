<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Response;

use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Response;
use PHPUnit\Util\Json;

class JsonRpcResponse implements Response
{
    /**
     * @var mixed|null
     */
    private $result;
    private ?JsonRpcError $jsonRpcError;
    private string|int|null $id;

    public function __construct(
        $result = null,
        JsonRpcError $jsonRpcError = null,
        string|int|null $id = null
    ) {
        $this->result = $result;
        $this->jsonRpcError = $jsonRpcError;
        $this->id = $id;
    }

    public static function success($result): Response
    {
        return new static($result);
    }

    public static function error(\Throwable $e): Response
    {
        $error = $e instanceof JsonRpcError
            ? $e
            : JsonRpcError::internalError('An error occurred during handling request', $e)
        ;

        return new static(null, $error);
    }

    public function getError(): ?JsonRpcError
    {
        return $this->jsonRpcError;
    }

    /**
     * @return mixed|null
     */
    public function getResult()
    {
        return $this->result;
    }

    public function hasError(): bool
    {
        return $this->jsonRpcError instanceof JsonRpcError;
    }

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function setId(int|string|null $id)
    {
        $this->id = $id;
    }
}
