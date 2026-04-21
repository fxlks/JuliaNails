<?php

namespace Http\Factory\Guzzle;

use AmeliaVendor\GuzzleHttp\Psr7\Request;
use AmeliaVendor\Psr\Http\Message\RequestFactoryInterface;
use AmeliaVendor\Psr\Http\Message\RequestInterface;

class RequestFactory implements RequestFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request($method, $uri);
    }
}
