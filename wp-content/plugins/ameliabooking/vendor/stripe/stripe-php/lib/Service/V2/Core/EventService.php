<?php

// File generated from our OpenAPI spec

namespace AmeliaVendor\Stripe\Service\V2\Core;

/**
 * @phpstan-import-type RequestOptionsArray from \Stripe\Util\RequestOptions
 *
 * @psalm-import-type RequestOptionsArray from \Stripe\Util\RequestOptions
 */
class EventService extends \AmeliaVendor\Stripe\Service\AbstractService
{
    /**
     * List events, going back up to 30 days.
     *
     * @param null|array{limit?: int, object_id: string} $params
     * @param null|RequestOptionsArray|\AmeliaVendor\Stripe\Util\RequestOptions $opts
     *
     * @return \AmeliaVendor\Stripe\V2\Collection<\AmeliaVendor\Stripe\V2\Event>
     *
     * @throws \AmeliaVendor\Stripe\Exception\ApiErrorException if the request fails
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v2/core/events', $params, $opts);
    }

    /**
     * Retrieves the details of an event.
     *
     * @param string $id
     * @param null|array $params
     * @param null|RequestOptionsArray|\AmeliaVendor\Stripe\Util\RequestOptions $opts
     *
     * @return \AmeliaVendor\Stripe\V2\Event
     *
     * @throws \AmeliaVendor\Stripe\Exception\ApiErrorException if the request fails
     */
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v2/core/events/%s', $id), $params, $opts);
    }
}
