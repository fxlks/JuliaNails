<?php

namespace AmeliaVendor\Stripe\Exception\OAuth;

/**
 * Implements properties and methods common to all (non-SPL) Stripe OAuth
 * exceptions.
 */
abstract class OAuthErrorException extends \AmeliaVendor\Stripe\Exception\ApiErrorException
{
    protected function constructErrorObject()
    {
        if (null === $this->jsonBody) {
            return null;
        }

        return \AmeliaVendor\Stripe\OAuthErrorObject::constructFrom($this->jsonBody);
    }
}
