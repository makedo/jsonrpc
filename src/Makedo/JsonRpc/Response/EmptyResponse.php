<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Response;

use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Response;

class EmptyResponse implements Response
{
    private Response $realResponse;

    public function __construct(Response $realResponse)
    {
        $this->realResponse = $realResponse;
    }

    public function getRealResponse(): Response
    {
        return $this->realResponse;
    }

    public function hasError(): bool
    {
        return $this->realResponse->hasError();
    }

    public function getError(): ?JsonRpcError
    {
        return $this->realResponse->getError();
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->realResponse->getResult();
    }

    public function getId(): string|int|null
    {
        return $this->realResponse->getId();
    }

    public function setId(int|string|null $id)
    {
        $this->realResponse->setId($id);
    }
}
