<?php declare(strict_types=1);

namespace Test\Makedo\JsonRpc\Json;

use Makedo\JsonRpc\Json\DefaultEncoder;
use Makedo\JsonRpc\Response\JsonRpcResponse;
use PHPUnit\Framework\TestCase;

class DefaultEncoderTest extends TestCase
{
    private DefaultEncoder $encoder;

    public function setUp(): void
    {
        $this->encoder = new DefaultEncoder();
    }

    /**
     * @dataProvider successfulEncodingDataProvider
     */
    public function testSuccessfulEncodingToJson($data, string $expectedJson)
    {
        $json = $this->encoder->encode($data);
        static::assertSame($expectedJson, $json);
    }

    public function successfulEncodingDataProvider()
    {
        return [
            [
                null,
                ''
            ],
            [
                [],
                '[]'
            ],
            [
                [[],[]],
                '[[],[]]'
            ],
            [
                [new \stdClass(), new \stdClass()],
                '[{},{}]'
            ],
            [
                new \stdClass(),
                '{}'
            ],
            [
                call_user_func(function() {
                    $obj = new \stdClass();
                    $obj->a = '{"a": 1}';
                    return $obj;
                }),
                '{"a":"{\"a\": 1}"}'
            ]
        ];
    }
}
