<?php declare(strict_types=1);

namespace Makedo\JsonRpc;

class Version
{
    private const NUMBER = '2.0';

    public static function number(): string
    {
        return self::NUMBER;
    }
}
