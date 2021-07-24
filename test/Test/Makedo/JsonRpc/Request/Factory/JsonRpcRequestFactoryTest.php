<?php declare(strict_types=1);

namespace Test\Makedo\JsonRpc\Request\Factory;

use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Request\JsonRpcRequest;
use Makedo\JsonRpc\Request\Factory\JsonRpcRequestFactory;
use PHPUnit\Framework\TestCase;

class JsonRpcRequestFactoryTest extends TestCase
{
    private JsonRpcRequestFactory $defaultRequestFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->defaultRequestFactory = new JsonRpcRequestFactory();
    }

    /**
     * @dataProvider validRequestDataProvider
     *
     * @param array $requestData
     * @throws JsonRpcError
     */
    public function testItCreatesRequest(array $requestData)
    {
        $request = $this->defaultRequestFactory->createRequest($requestData);

        static::assertInstanceOf(JsonRpcRequest::class, $request);
        static::assertSame($requestData['id'] ?? null, $request->getId());
        static::assertSame($requestData['method'], $request->getMethod());
        static::assertSame($requestData['params'] ?? [], $request->getParams());
        static::assertSame($requestData['jsonrpc'], $request->getJsonrpc());
    }

    public function validRequestDataProvider()
    {
        return [
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'params' => ['a' => 1],
                'id' => '11',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => '1',
                'params' => ['a' => 1],
                'id' => '11',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'params' => ['a' => 1],
                'id' => 11,
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest3',
                'params' => ['a' => 3],
                'id' => null,
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest2',
                'params' => ['a' => 2],
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest3',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest3',
                'id'  => '0',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'id' => '',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'id' => 'a',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'id' => '1.0',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'id' => '0.0',
            ]],
        ];
    }

    /**
     * @dataProvider invalidRequestDataProvider
     *
     * @param array $requestData
     * @throws JsonRpcError
     */
    public function testItThrowsInvalidRequestJsonRpcError(array $requestData)
    {
        static::expectException(JsonRpcError::class);
        $this->defaultRequestFactory->createRequest($requestData);
    }

    public function invalidRequestDataProvider()
    {
        return [
            //Invalid version (jsonrpc)
            [[
                //'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'params' => ['a' => 1],
                'id' => '11',
            ]],
            [[
                'jsonrpc' => '1.0', //Invalid
                'method'  => 'gimmerequest',
                'params' => ['a' => 1],
                'id' => '11',
            ]],
            [[
                'jsonrpc' => null, //Invalid
                'method'  => 'gimmerequest',
                'params' => ['a' => 1],
                'id' => '11',
            ]],
            [[
                'jsonrpc' => 1.0, //Invalid
                'method'  => 'gimmerequest',
                'params' => ['a' => 1],
                'id' => '11',
            ]],
            [[
                'jsonrpc' => 2, //Invalid
                'method'  => 'gimmerequest',
                'params' => ['a' => 1],
                'id' => '11',
            ]],
            [[
                'jsonrpc' => ['a'], //Invalid
                'method'  => 'gimmerequest',
                'params' => ['a' => 1],
                'id' => '11',
            ]],

            //Invalid method
            [[
                'jsonrpc' => '2.0',
                //'method'  => 'gimmerequest', //Invalid
                'params' => ['a' => 1],
                'id' => '11',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 2, //Invalid
                'params' => ['a' => 1],
                'id' => '11',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => null, //Invalid
                'params' => ['a' => 1],
                'id' => '11',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => '', //Invalid
                'params' => ['a' => 1],
                'id' => '11',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => [], //Invalid
                'params' => ['a' => 1],
                'id' => '11',
            ]],

            //Invalid params
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'params' => 1, //Invalid
                'id' => '11',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'params' => null, //Invalid
                'id' => '11',
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'params' => '', //Invalid
                'id' => '11',
            ]],

            //Invalid id
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'id' => [], //Invalid
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'id' => 1.0, //Invalid
            ]],
            [[
                'jsonrpc' => '2.0',
                'method'  => 'gimmerequest',
                'id' => 0.0, //Invalid
            ]],
        ];
    }
}
