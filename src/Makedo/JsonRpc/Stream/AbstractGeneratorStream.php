<?php declare(strict_types=1);

namespace Makedo\JsonRpc\Stream;

use Psr\Http\Message\StreamInterface;

abstract class AbstractGeneratorStream implements StreamInterface
{
    protected ?\Generator $generator;

    public function __construct(\Generator $generator)
    {
        $this->generator = $generator;
    }

    public function __toString()
    {
        return $this->getContents();
    }

    public function close()
    {
        $this->detach();
    }

    public function detach()
    {
        $generator = $this->generator;
        $this->generator = null;
        return $generator;
    }

    public function getSize()
    {
        return null;
    }

    public function tell()
    {
        throw new \RuntimeException('Not tellable');
    }

    public function eof()
    {
        return !$this->generator->valid();
    }

    public function isSeekable()
    {
        return false;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        throw new \RuntimeException('Not seekable');
    }

    public function rewind()
    {
        $this->generator->rewind();
    }

    public function isWritable()
    {
        return false;
    }

    public function write($string)
    {
        throw new \RuntimeException('Not writable');
    }

    public function isReadable()
    {
        return true;
    }

    public function getMetadata($key = null)
    {
        $metadata = [
            'eof' => $this->eof(),
            'stream_type' => 'generator',
            'seekable' => false,
        ];

        if (null === $key) {
            return $metadata;
        }

        if (!array_key_exists($key, $metadata)) {
            return null;
        }

        return $metadata[$key];
    }

    abstract public function read($length);
    abstract public function getContents();
}
