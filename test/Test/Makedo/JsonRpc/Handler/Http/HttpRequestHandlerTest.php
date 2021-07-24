<?php declare(strict_types=1);

namespace Test\Makedo\JsonRpc\Handler\Http;

use Laminas\Diactoros\ResponseFactory;

use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Handler\HandlerBuilder;
use Makedo\JsonRpc\Handler\Http\HttpRequestHandler;
use Makedo\JsonRpc\Handler\Request\CallbackHandler;
use Makedo\JsonRpc\Request\JsonRpcRequest;

use Makedo\JsonRpc\Response\JsonRpcResponse;
use PHPUnit\Framework\TestCase;

use Psr\Http\Server\RequestHandlerInterface;
use Test\Makedo\JsonRpc\TestHelper;

class HttpRequestHandlerTest extends TestCase
{
    private RequestHandlerInterface $handler;

    public function setUp(): void
    {
        $this->handler =  $httpHandler = (new HandlerBuilder())->buildHttpRequestHandler(
            new CallbackHandler(function (JsonRpcRequest $request): JsonRpcResponse {
                switch ($request->getMethod()) {
                    case 'sum':
                        $result = array_reduce($request->getParams(), function (int $a, int $b) {
                            return $a + $b;
                        }, 0);
                        break;
                    case 'subtract':
                        $result =  -array_reduce($request->getParams(), function (int $a, int $b) {
                            return $b - $a;
                        }, 0);
                        break;
                    case 'get_data':
                        $result = ["data"];
                        break;
                    default:
                        throw JsonRpcError::methodNotFound(sprintf('Method %s not found', $request->getMethod()));
                }

                return new JsonRpcResponse($result);
            }),
            new ResponseFactory()
        );
    }

    /**
     * @dataProvider requestDataProvider
     */
    public function testRequestHandler(string $requestBody, string $expectedResponseBody)
    {
        $request = TestHelper::createHttpRequest($requestBody);

        $response = $this->handler->handle($request);

        static::assertSame($expectedResponseBody, $response->getBody()->getContents());
        static::assertSame('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function requestDataProvider()
    {
        return [
            [
                '{"jsonrpc": "2.0", "method": "sum", "params": [1, 2, 16], "id": 1}',
                '{"jsonrpc":"2.0","result":19,"id":1}'
            ],
            [
                '{"jsonrpc":"2.0", "method": "sum", "params": [1, 2, 16], "id": 1',
                '{"jsonrpc":"2.0","error":{"code":-32700,"message":"Parse error"},"id":null}'
            ],
            [
                '{"jsonrpc": "1.0", "method": "sum", "params": [1, 2, 16], "id": 1}',
                '{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"},"id":null}',
            ],
            [
                '{"jsonrpc": "2.0", "method": "sum", "params": ["a"], "id": 1}',
                '{"jsonrpc":"2.0","error":{"code":-32603,"message":"Internal error"},"id":1}'
            ],
            [
                '{"jsonrpc": "2.0", "method": "sum", "params": ["a"]}',
                ''
            ],
            [
                '{"jsonrpc": "2.0", "method": "sum", "params": [1, 2, 16]}',
                ''
            ],
            [
                '{"jsonrpc": "2.0", "method": "sum", "params": ["a"]}',
                ''
            ],
        ];
    }

    /**
     * @dataProvider batchRequestDataProvider
     */
    public function testBatchRequestHandler(string $requestBody, string $expectedResponseBody)
    {
        $httpRequest =  TestHelper::createHttpRequest($requestBody);

        $response = $this->handler->handle($httpRequest);

        static::assertSame($expectedResponseBody, $response->getBody()->getContents());
        static::assertSame('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function batchRequestDataProvider()
    {
        return [
            [
                '[
                    {"jsonrpc": "2.0", "method": "sum", "params": [1, 2, 16], "id": 1}
                ]',
                '[{"jsonrpc":"2.0","result":19,"id":1}]',
            ],

            [
                '[
                    {"jsonrpc": "2.0", "method": "sum", "params": [1, 2, 16], "id": 1},
                    {"jsonrpc": "2.0", "method": "sum", "params": [1, 2, 16], "id": 2}
                ]',
                '[{"jsonrpc":"2.0","result":19,"id":1},{"jsonrpc":"2.0","result":19,"id":2}]',
            ],

            [
                '[
                    {"jsonrpc": "2.0", "method": "sum", "params": [1, 2, 16], "id": 1},
                    {"jsonrpc": "2.0", "method": "sum", "params": [1, 2, 16]},
                    {"jsonrpc": "2.0", "method": "sum", "params": [1, 2, 16], "id": 3}
                ]',
                '[{"jsonrpc":"2.0","result":19,"id":1},{"jsonrpc":"2.0","result":19,"id":3}]',
            ],

            [
                '[
                    {"jsonrpc": "2.0", "method": "sum", "params": [1, 2, 16]},
                    {"jsonrpc": "2.0", "method": "sum", "params": [1, 2, 16]}
                ]',
                '',
            ],
            [
                '[
                    {"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},
                    {"jsonrpc": "2.0", "method": "sum", "params": [19]},
                    {"jsonrpc": "2.0", "method": "subtract", "params": [42,23], "id": "2"},
                    {"foo": "boo"},
                    {"jsonrpc": "2.0", "method": "foo.get", "params": {"name": "myself"}, "id": "5"},
                    {"jsonrpc": "2.0", "method": "get_data", "id": "9"} 
                ]',
                '[{"jsonrpc":"2.0","result":7,"id":"1"},{"jsonrpc":"2.0","result":19,"id":"2"},{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"},"id":null},{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"},"id":"5"},{"jsonrpc":"2.0","result":["data"],"id":"9"}]'
            ]
        ];
    }

}
