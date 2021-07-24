<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Response\Serializer\Result;

interface ResultSerializer
{
    /**
     * @param mixed $result
     * @return mixed
     */
    public function serialize($result);
}
