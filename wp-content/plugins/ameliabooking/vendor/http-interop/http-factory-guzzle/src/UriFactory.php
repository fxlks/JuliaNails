<?php

namespace Http\Factory\Guzzle;

use AmeliaVendor\GuzzleHttp\Psr7\Uri;
use AmeliaVendor\Psr\Http\Message\UriFactoryInterface;
use AmeliaVendor\Psr\Http\Message\UriInterface;

class UriFactory implements UriFactoryInterface
{
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}
