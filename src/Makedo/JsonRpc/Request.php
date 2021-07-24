<?php declare(strict_types=1);

namespace Makedo\JsonRpc;

interface Request
{
    public function getJsonrpc(): string;
    public function getMethod(): string;
    public function getParams(): array;
    public function getId(): string|int|null;
    public function isNotification(): bool;
}
