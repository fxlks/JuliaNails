<?php

namespace AmeliaHttp\Message\StreamFactory;

use AmeliaHttp\Message\StreamFactory;

/**
 * Creates Guzzle streams.
 *
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 */
final class GuzzleStreamFactory implements StreamFactory
{
    /**
     * {@inheritdoc}
     */
    public function createStream($body = null)
    {
        return \AmeliaVendor\GuzzleHttp\Psr7\Utils::streamFor($body);
    }
}
