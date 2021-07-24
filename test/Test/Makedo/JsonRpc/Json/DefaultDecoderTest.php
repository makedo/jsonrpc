<?php declare(strict_types=1);

namespace Test\Makedo\JsonRpc\Json;

use Makedo\JsonRpc\Exception\JsonRpcError;
use Makedo\JsonRpc\Json\DefaultDecoder;
use PHPUnit\Framework\TestCase;

class DefaultDecoderTest extends TestCase
{
    private DefaultDecoder $decoder;

    public function setUp(): void
    {
        $this->decoder = new DefaultDecoder();
    }

    /**
     * @dataProvider successfulEncodingDataProvider
     */
    public function testSuccessfulDecodingFromJson(string $json, array $expectedData)
    {
        $data = $this->decoder->decode($json);
        static::assertSame($expectedData, $data);
    }

    public function successfulEncodingDataProvider()
    {
        return [
            [
                '[[],[]]',
                [[],[]],
            ],
            [
                '[[]]',
                [[]],
            ],
            [
                '{"jsonrpc":"2.0","method":"method","params":[ {"a":1, "b":null, "c": 1.0}, {"d":"d"}, {"e":[1,2,3]}], "id":null}',
                [
                    'jsonrpc' => '2.0',
                    'method' => 'method',
                    'params' => [
                        ['a' => 1, 'b' => null, 'c' => 1.0],
                        ['d' => 'd'],
                        ['e' => [1,2,3]],
                    ],
                    'id' => null,
                ],
            ],
        ];
    }

    /**
     * @dataProvider errorEncodingDataProvider
     */
    public function testErrorDecodingFromJson(string $json)
    {
        static::expectException(JsonRpcError::class);
        $this->decoder->decode($json);
    }

    public function errorEncodingDataProvider()
    {
        return [
            [''],
            ['[]'],
            ['{}'],
            ['null'],
            ['{'],
            ['eat my shorts'],
        ];
    }
}
