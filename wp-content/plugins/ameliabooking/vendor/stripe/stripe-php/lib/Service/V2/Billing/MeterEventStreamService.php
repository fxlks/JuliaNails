<?php

// File generated from our OpenAPI spec

namespace AmeliaVendor\Stripe\Service\V2\Billing;

/**
 * @phpstan-import-type RequestOptionsArray from \Stripe\Util\RequestOptions
 *
 * @psalm-import-type RequestOptionsArray from \Stripe\Util\RequestOptions
 */
class MeterEventStreamService extends \AmeliaVendor\Stripe\Service\AbstractService
{
    /**
     * Creates meter events. Events are processed asynchronously, including validation.
     * Requires a meter event session for authentication. Supports up to 10,000
     * requests per second in livemode. For even higher rate-limits, contact sales.
     *
     * @param null|array{events: array{event_name: string, identifier?: string, payload: array<string, string>, timestamp?: string}[]} $params
     * @param null|RequestOptionsArray|\AmeliaVendor\Stripe\Util\RequestOptions $opts
     *
     * @return void
     *
     * @throws \AmeliaVendor\Stripe\Exception\TemporarySessionExpiredException
     */
    public function create($params = null, $opts = null)
    {
        $opts = \AmeliaVendor\Stripe\Util\RequestOptions::parse($opts);
        if (!isset($opts->apiBase)) {
            $opts->apiBase = $this->getClient()->getMeterEventsBase();
        }
        $this->request('post', '/v2/billing/meter_event_stream', $params, $opts);
    }
}
