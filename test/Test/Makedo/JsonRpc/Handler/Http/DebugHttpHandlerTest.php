<?php declare(strict_types=1);

namespace Test\Makedo\JsonRpc\Handler\Http;

use Laminas\Diactoros\ResponseFactory;
use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Handler\HandlerBuilder;
use Makedo\JsonRpc\Handler\Request\CallbackHandler;
use Makedo\JsonRpc\Request;
use Makedo\JsonRpc\Version;
use PHPUnit\Framework\TestCase;
use Test\Makedo\JsonRpc\TestHelper;

class DebugHttpHandlerTest extends TestCase
{
    /**
     * @dataProvider debugResponseHandlerDataProvider
     */
    public function testDebugResponseHandlerTest(string $requestBody, string $expectedResponseBody)
    {
        $request = TestHelper::createHttpRequest($requestBody);
        $httpHandler = (new HandlerBuilder())->buildHttpRequestHandler(
            new CallbackHandler(function (Request $request) {
                throw JsonRpcError::invalidParams('Debug params', ['params' => ['key' => 'Invalid']]);
            }),
            new ResponseFactory(),
            true
        );

        $response = $httpHandler->handle($request);

        $responseData = json_decode($response->getBody()->getContents(), true);
        static::assertSame(Version::number(), $responseData['jsonrpc']);
        static::assertSame('5', $responseData['id']);
        static::assertSame(-32602, $responseData['error']['code']);
        static::assertSame('Invalid params', $responseData['error']['message']);
        static::assertSame('Debug params', $responseData['error']['data']['debug']['debugMessage']);
        static::assertSame(null, $responseData['error']['data']['debug']['previousMessage']);
        static::assertSame(['key' => 'Invalid'], $responseData['error']['data']['params']);
        static::assertNotEmpty($responseData['error']['data']['debug']['trace']);
    }

    public function debugResponseHandlerDataProvider()
    {
        return [
            [
                '{"jsonrpc": "2.0", "method": "foo.get", "params": {"name": "myself"}, "id": "5"}',
                '{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"}'
            ]
        ];
    }
}
