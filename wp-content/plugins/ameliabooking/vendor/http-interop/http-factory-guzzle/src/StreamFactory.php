<?php

namespace Http\Factory\Guzzle;

use AmeliaVendor\GuzzleHttp\Psr7\Stream;
use AmeliaVendor\GuzzleHttp\Psr7\Utils;
use AmeliaVendor\Psr\Http\Message\StreamFactoryInterface;
use AmeliaVendor\Psr\Http\Message\StreamInterface;

class StreamFactory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        return Utils::streamFor($content);
    }

    public function createStreamFromFile(string $file, string $mode = 'r'): StreamInterface
    {
        return $this->createStreamFromResource(Utils::tryFopen($file, $mode));
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
