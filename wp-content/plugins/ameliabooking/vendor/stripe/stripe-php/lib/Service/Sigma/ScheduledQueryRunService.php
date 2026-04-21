<?php

// File generated from our OpenAPI spec

namespace AmeliaVendor\Stripe\Service\Sigma;

/**
 * @phpstan-import-type RequestOptionsArray from \Stripe\Util\RequestOptions
 *
 * @psalm-import-type RequestOptionsArray from \Stripe\Util\RequestOptions
 */
class ScheduledQueryRunService extends \AmeliaVendor\Stripe\Service\AbstractService
{
    /**
     * Returns a list of scheduled query runs.
     *
     * @param null|array{ending_before?: string, expand?: string[], limit?: int, starting_after?: string} $params
     * @param null|RequestOptionsArray|\AmeliaVendor\Stripe\Util\RequestOptions $opts
     *
     * @return \AmeliaVendor\Stripe\Collection<\AmeliaVendor\Stripe\Sigma\ScheduledQueryRun>
     *
     * @throws \AmeliaVendor\Stripe\Exception\ApiErrorException if the request fails
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/sigma/scheduled_query_runs', $params, $opts);
    }

    /**
     * Retrieves the details of an scheduled query run.
     *
     * @param string $id
     * @param null|array{expand?: string[]} $params
     * @param null|RequestOptionsArray|\AmeliaVendor\Stripe\Util\RequestOptions $opts
     *
     * @return \AmeliaVendor\Stripe\Sigma\ScheduledQueryRun
     *
     * @throws \AmeliaVendor\Stripe\Exception\ApiErrorException if the request fails
     */
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/sigma/scheduled_query_runs/%s', $id), $params, $opts);
    }
}
