<?php

namespace AmeliaHttp\Message\UriFactory;

use AmeliaVendor\GuzzleHttp\Psr7;
use AmeliaHttp\Message\UriFactory;

/**
 * Creates Guzzle URI.
 *
 * @author David de Boer <david@ddeboer.nl>
 */
final class GuzzleUriFactory implements UriFactory
{
    /**
     * {@inheritdoc}
     */
    public function createUri($uri)
    {
        return Psr7\Utils::uriFor($uri);
    }
}
