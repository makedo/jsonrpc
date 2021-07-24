<?php declare(strict_types=1);

namespace Test\Makedo\JsonRpc;

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Stream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class TestHelper
{
    public static function createHttpRequest(string $requestBody): ServerRequestInterface
    {
        return (new ServerRequestFactory())
            ->createServerRequest('POST', 'http://test.test')
            ->withBody(self::wrapRequestBodyWithStream($requestBody))
            ;
    }

    public static function wrapRequestBodyWithStream(string $requestBody): StreamInterface
    {
        $stream = fopen('php://memory', 'rw+');
        fwrite($stream, $requestBody);
        rewind($stream);

        return new Stream($stream);
    }
}
