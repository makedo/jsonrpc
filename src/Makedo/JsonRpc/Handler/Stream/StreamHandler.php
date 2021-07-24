<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Handler\Stream;

use Psr\Http\Message\StreamInterface;

interface StreamHandler
{
    public function handle(StreamInterface $stream): StreamInterface;
}
