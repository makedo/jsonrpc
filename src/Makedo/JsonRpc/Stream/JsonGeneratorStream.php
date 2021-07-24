<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Stream;

use Makedo\JsonRpc\Json\Encoder;
use Makedo\JsonRpc\Response\Serializer\ResponseSerializer;

class JsonGeneratorStream extends AbstractGeneratorStream
{
    protected Encoder $encoder;
    protected bool $isBatch;
    protected ResponseSerializer $responseSerializer;

    public function __construct(
        \Generator $generator,
        Encoder $encoder,
        ResponseSerializer $responseSerializer,
        bool $isBatch
    ) {
        parent::__construct($generator);

        $this->isBatch = $isBatch;
        $this->responseSerializer = $responseSerializer;
        $this->encoder = $encoder;
    }

    public function read($length)
    {
        $result = [];
        for ($i = 0; $i < $length; ++$i) {
            if (!$this->generator->valid()) {
                break;
            }
            $result[] = $this->current();
            $this->generator->next();
        }

        return join(',', $result);
    }

    public function getContents()
    {
        $result = [];
        while ($this->generator->valid()) {
            $result[] = $this->current();
            $this->generator->next();
        }

        $result = array_filter($result);
        if (empty($result)) {
            return '';
        }

        $response = join(',', $result);
        return $this->isBatch ? '[' . $response . ']' : $response;
    }

    protected function current(): string
    {
        $response = $this->generator->current();
        $serializedResponse = $this->responseSerializer->serialize($response);
        return $this->encoder->encode($serializedResponse);
    }
}
