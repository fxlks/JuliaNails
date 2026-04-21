<?php

namespace AmeliaHttp\Client\Curl;

use AmeliaHttp\Message\Builder\ResponseBuilder as OriginalResponseBuilder;
use AmeliaVendor\Psr\Http\Message\ResponseInterface;

/**
 * Extended response builder.
 */
class ResponseBuilder extends OriginalResponseBuilder
{
    /**
     * Replace response with a new instance.
     *
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
