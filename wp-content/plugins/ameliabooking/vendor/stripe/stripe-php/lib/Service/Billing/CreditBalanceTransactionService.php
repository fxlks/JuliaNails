<?php

// File generated from our OpenAPI spec

namespace AmeliaVendor\Stripe\Service\Billing;

/**
 * @phpstan-import-type RequestOptionsArray from \Stripe\Util\RequestOptions
 *
 * @psalm-import-type RequestOptionsArray from \Stripe\Util\RequestOptions
 */
class CreditBalanceTransactionService extends \AmeliaVendor\Stripe\Service\AbstractService
{
    /**
     * Retrieve a list of credit balance transactions.
     *
     * @param null|array{credit_grant?: string, customer: string, ending_before?: string, expand?: string[], limit?: int, starting_after?: string} $params
     * @param null|RequestOptionsArray|\AmeliaVendor\Stripe\Util\RequestOptions $opts
     *
     * @return \AmeliaVendor\Stripe\Collection<\AmeliaVendor\Stripe\Billing\CreditBalanceTransaction>
     *
     * @throws \AmeliaVendor\Stripe\Exception\ApiErrorException if the request fails
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/billing/credit_balance_transactions', $params, $opts);
    }

    /**
     * Retrieves a credit balance transaction.
     *
     * @param string $id
     * @param null|array{expand?: string[]} $params
     * @param null|RequestOptionsArray|\AmeliaVendor\Stripe\Util\RequestOptions $opts
     *
     * @return \AmeliaVendor\Stripe\Billing\CreditBalanceTransaction
     *
     * @throws \AmeliaVendor\Stripe\Exception\ApiErrorException if the request fails
     */
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/billing/credit_balance_transactions/%s', $id), $params, $opts);
    }
}
