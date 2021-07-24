<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Handler\Stream;

use Makedo\JsonRpc\Handler\Request\RequestHandler;
use Makedo\JsonRpc\Response\Serializer\ResponseSerializer;
use Makedo\JsonRpc\Stream\JsonGeneratorStream;
use Makedo\JsonRpc\Json\Decoder;
use Makedo\JsonRpc\Json\Encoder;
use Makedo\JsonRpc\Request\Factory\RequestFactory;
use Makedo\JsonRpc\Response\JsonRpcResponse;
use Psr\Http\Message\StreamInterface;

class JsonGeneratorStreamHandler implements StreamHandler
{
    protected Encoder $encoder;
    protected Decoder $decoder;

    protected RequestFactory $requestFactory;
    protected RequestHandler $requestHandler;
    protected ResponseSerializer $responseSerializer;

    public function __construct(
        Encoder $encoder,
        Decoder $decoder,
        RequestFactory $requestFactory,
        RequestHandler $requestHandler,
        ResponseSerializer $responseSerializer
    ) {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->requestHandler = $requestHandler;
        $this->requestFactory = $requestFactory;
        $this->responseSerializer = $responseSerializer;
    }

    public function handle(StreamInterface $stream): StreamInterface
    {
        $isBatch = false;

        try {
            $isBatch = $this->isBatch($stream);
            $arrayRequests = $this->decodeStream($stream, $isBatch);
            $generator = $this->createRequestHandlerGenerator($arrayRequests);
        } catch (\Throwable $e) {
            $generator = call_user_func(fn() => yield JsonRpcResponse::error($e));
        }

        return new JsonGeneratorStream($generator, $this->encoder, $this->responseSerializer, $isBatch);
    }

    protected function decodeStream(StreamInterface $stream, bool $isBatch): array
    {
        $stringRequest = $stream->getContents();
        $arrayRequests = $this->decoder->decode($stringRequest);
        return $isBatch ? $arrayRequests : [$arrayRequests];
    }

    protected function createRequestHandlerGenerator(array $arrayRequests): \Generator
    {
        foreach ($arrayRequests as $requestData) {
            try {
                $request = $this->requestFactory->createRequest($requestData);
                yield $this->requestHandler->handle($request);
            } catch (\Throwable $e) {
                yield JsonRpcResponse::error($e);
            }
        }
    }

    protected function isBatch(StreamInterface $stream): bool
    {
        $firstSymbol = $stream->read(1);

        $stream->rewind();

        return $firstSymbol === '[';
    }
}
