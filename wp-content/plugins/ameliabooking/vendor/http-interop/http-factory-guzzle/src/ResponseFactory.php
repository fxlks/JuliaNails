<?php

namespace Http\Factory\Guzzle;

use AmeliaVendor\GuzzleHttp\Psr7\Response;
use AmeliaVendor\Psr\Http\Message\ResponseFactoryInterface;
use AmeliaVendor\Psr\Http\Message\ResponseInterface;

class ResponseFactory implements ResponseFactoryInterface
{
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response($code, [], null, '1.1', $reasonPhrase);
    }
}
