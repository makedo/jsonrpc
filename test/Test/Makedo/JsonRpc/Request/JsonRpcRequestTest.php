<?php declare(strict_types=1);

namespace Test\Makedo\JsonRpc\Request;

use Makedo\JsonRpc\Request\JsonRpcRequest;
use Makedo\JsonRpc\Version;
use PHPUnit\Framework\TestCase;

class JsonRpcRequestTest extends TestCase
{
    public function testRequestIsNotificationWhenIdIsNull()
    {
        $request = new JsonRpcRequest(
            Version::number(),
            'doSomething',
            []
        );

        static::assertTrue($request->isNotification());
    }

    public function testRequestIsNotNotificationWhenIdIsNotNull()
    {
        $request = new JsonRpcRequest(
            Version::number(),
            'doSomething',
            [],
            '123'
        );

        static::assertFalse($request->isNotification());
    }

    public function testRequestHasProperties()
    {
        $jsonrpc = Version::number();
        $method = 'doSomething';
        $params = ['a' => 1];
        $id = 2;

        $request = new JsonRpcRequest(
            $jsonrpc,
            $method,
            $params,
            $id
        );

        static::assertSame($jsonrpc, $request->getJsonrpc());
        static::assertSame($method, $request->getMethod());
        static::assertSame($params, $request->getParams());
        static::assertSame($id, $request->getId());
    }
}
