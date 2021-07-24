<?php declare(strict_types=1);

namespace Makedo\JsonRpc;

use Makedo\JsonRpc\Exception\JsonRpcError;

interface Response
{
    public function hasError(): bool;
    public function getError(): ?JsonRpcError;

    public function getId(): string|int|null;
    public function setId(string|int|null $id);

    /**
     * @return mixed
     */
    public function getResult();
}
