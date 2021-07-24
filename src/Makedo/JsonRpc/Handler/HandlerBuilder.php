<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Handler;

use Makedo\JsonRpc\Exception\Serializer\ErrorSerializer;
use Makedo\JsonRpc\Exception\Serializer\JsonRpcErrorSerializer;
use Makedo\JsonRpc\Handler\Http\HttpRequestHandler;
use Makedo\JsonRpc\Handler\Request\HandlerDecorator;
use Makedo\JsonRpc\Handler\Request\RequestHandler;
use Makedo\JsonRpc\Handler\Stream\JsonGeneratorStreamHandler;
use Makedo\JsonRpc\Json\Decoder;
use Makedo\JsonRpc\Json\Encoder;
use Makedo\JsonRpc\Json\DefaultDecoder;
use Makedo\JsonRpc\Json\DefaultEncoder;
use Makedo\JsonRpc\Request\Factory\JsonRpcRequestFactory;
use Makedo\JsonRpc\Request\Factory\RequestFactory;
use Makedo\JsonRpc\Response\Serializer\JsonRpcResponseSerializer;
use Makedo\JsonRpc\Response\Serializer\Result\JsonRpcResultSerializer;
use Makedo\JsonRpc\Response\Serializer\Result\ResultSerializer;
use Psr\Http\Message\ResponseFactoryInterface;

class HandlerBuilder
{
    private ?Encoder $encoder = null;
    private ?Decoder $decoder = null;
    private ?RequestFactory $requestFactory = null;
    private ?ErrorSerializer $errorSerializer = null;
    private ?ResultSerializer $resultSerializer = null;

    public function setJsonEncoder(Encoder $encoder): HandlerBuilder
    {
        $this->encoder = $encoder;
        return $this;
    }

    public function setJsonDecoder(Decoder $decoder): HandlerBuilder
    {
        $this->decoder = $decoder;
        return $this;
    }

    public function setRequestFactory(RequestFactory $requestFactory): HandlerBuilder
    {
        $this->requestFactory = $requestFactory;
        return $this;
    }

    public function setErrorSerializer(ErrorSerializer $errorSerializer): HandlerBuilder
    {
        $this->errorSerializer = $errorSerializer;
        return $this;
    }

    public function setResultSerializer(ResultSerializer $resultSerializer): HandlerBuilder
    {
        $this->resultSerializer = $resultSerializer;
        return $this;
    }

    public function buildJsonGeneratorStreamHandler(RequestHandler $jsonRpcRequestHandler, bool $debug = false)
    {
        return new JsonGeneratorStreamHandler(
            $this->getJsonEncoder(),
            $this->getJsonDecoder(),
            $this->getJsonRpcRequestFactory(),
            $this->getJsonRpcRequestHandler($jsonRpcRequestHandler),
            $this->getJsonRpcResponseSerializer($debug)
        );
    }

    public function buildHttpRequestHandler(
        RequestHandler $jsonRpcRequestHandler,
        ResponseFactoryInterface $httpResponseFactory,
        bool $debug = false
    ) {
        return new HttpRequestHandler(
            $this->buildJsonGeneratorStreamHandler($jsonRpcRequestHandler, $debug),
            $httpResponseFactory,
        );
    }

    private function getJsonEncoder(): Encoder
    {
        return $this->encoder ?? new DefaultEncoder();
    }

    private function getJsonDecoder(): Decoder
    {
        return $this->decoder ?? new DefaultDecoder();
    }

    private function getJsonRpcRequestFactory(): RequestFactory
    {
        return $this->requestFactory ?? new JsonRpcRequestFactory();
    }

    private function getJsonRpcRequestHandler(RequestHandler $requestHandler): HandlerDecorator
    {
        return new HandlerDecorator($requestHandler);
    }

    private function getJsonRpcResponseSerializer(bool $debug): JsonRpcResponseSerializer
    {
        return new JsonRpcResponseSerializer(
            $this->errorSerializer ?? JsonRpcErrorSerializer::create($debug),
            $this->resultSerializer ?? new JsonRpcResultSerializer()
        );
    }
}
