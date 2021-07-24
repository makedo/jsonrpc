<?php declare(strict_types=1);

namespace Test\Makedo\JsonRpc\Response\Serializer;

use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Exception\Serializer\JsonRpcErrorSerializer;
use Makedo\JsonRpc\Response;
use Makedo\JsonRpc\Response\JsonRpcResponse;
use Makedo\JsonRpc\Response\EmptyResponse;
use Makedo\JsonRpc\Response\Serializer\JsonRpcResponseSerializer;
use Makedo\JsonRpc\Response\Serializer\Result\JsonRpcResultSerializer;
use Makedo\JsonRpc\Version;
use PHPUnit\Framework\TestCase;

class JsonRpcResponseSerializerTest extends TestCase
{
    private JsonRpcResponseSerializer $serializer;

    public function setUp(): void
    {
        $this->serializer = new JsonRpcResponseSerializer(
            JsonRpcErrorSerializer::create(),
            new JsonRpcResultSerializer(),
        );
    }

    /**
     * @dataProvider responseDataProvider
     */
    public function testItSerializesResponseToArray(Response $response, array $expectedSerializedResponse)
    {
        static::assertSame($expectedSerializedResponse, $this->serializer->serialize($response));
    }

    public function responseDataProvider()
    {
        return [
            [
                new JsonRpcResponse(
                    null,
                    JsonRpcError::internalError('An error occurred')
                ),
                [
                    'jsonrpc' => Version::number(),
                    'error' => [
                        'code' => -32603,
                        'message' => 'Internal error',
                    ],
                    'id' => null,
                ],
            ],
            [
                new JsonRpcResponse(
                    null,
                    JsonRpcError::internalError('An error occurred'),
                    '1',
                ) ,
                [
                    'jsonrpc' => Version::number(),
                    'error' => [
                        'code' => -32603,
                        'message' => 'Internal error',
                    ],
                    'id' => '1',
                ]
            ],

            [
                new JsonRpcResponse(['success' => true], null, '2',),
                [
                    'jsonrpc' => Version::number(),
                    'result' => ['success' => true],
                    'id' => '2',
                ]
            ],
            [
                new JsonRpcResponse(2, null, '3'),
                [
                    'jsonrpc' => Version::number(),
                    'result' => 2,
                    'id' => '3',
                ]
            ],
            [
                new JsonRpcResponse(0.5, null, '4',),
                [
                    'jsonrpc' => Version::number(),
                    'result' => 0.5,
                    'id' => '4',
                ]
            ],
            [
                new JsonRpcResponse('HELLO', null, 5,),
                [
                    'jsonrpc' => Version::number(),
                    'result' => 'HELLO',
                    'id' => 5,
                ]
            ]
        ];
    }

    public function testItSerializesEmptyResponseToNull()
    {
        $response = new EmptyResponse(new JsonRpcResponse(['success' => true], null, '1',));
        static::assertNull($this->serializer->serialize($response));
    }
}
