<?php

namespace AmeliaBooking\Domain\Services\Api;

/**
 * Class BasicApiService
 *
 * @package AmeliaBooking\Domain\Services\Api
 */
class BasicApiService
{
    /**
     * @param $apiKey
     * @param $hashedKeys
     *
     * @return boolean
     */
    public function checkApiKeys($apiKey, $hashedKeys)
    {
        return false;
    }

    /**
     * @param $apiKey
     *
     * @return string
     */
    public function createHash($apiKey)
    {
        return '';
    }
}
