<?php

// File generated from our OpenAPI spec

namespace AmeliaVendor\Stripe\Events;

/**
 * @property \AmeliaVendor\Stripe\RelatedObject $related_object Object containing the reference to API resource relevant to the event
 */
class V2CoreEventDestinationPingEvent extends \AmeliaVendor\Stripe\V2\Event
{
    const LOOKUP_TYPE = 'v2.core.event_destination.ping';

    /**
     * Retrieves the related object from the API. Make an API request on every call.
     *
     * @return \AmeliaVendor\Stripe\V2\EventDestination
     *
     * @throws \AmeliaVendor\Stripe\Exception\ApiErrorException if the request fails
     */
    public function fetchRelatedObject()
    {
        $apiMode = \AmeliaVendor\Stripe\Util\Util::getApiMode($this->related_object->url);
        list($object, $options) = $this->_request(
            'get',
            $this->related_object->url,
            [],
            ['stripe_account' => $this->context],
            [],
            $apiMode
        );

        return \AmeliaVendor\Stripe\Util\Util::convertToStripeObject($object, $options, $apiMode);
    }
}
