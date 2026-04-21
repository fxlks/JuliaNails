<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Services\PayPal;

/**
 * Class PayPalResponse
 *
 */
class PayPalResponse
{
    /**
     * HTTP status codes that indicate a successful API call.
     */
    private const SUCCESS_CODES = [200, 201, 204];

    /**
     * @var array
     */
    private array $data;

    /**
     * @var int
     */
    private int $httpCode;

    /**
     * PayPalResponse constructor.
     *
     */
    public function __construct(array $data)
    {
        $this->data     = $data;
        $this->httpCode = isset($data['_http_code']) ? (int)$data['_http_code'] : 0;
    }

    /**
     * Whether the request was successful.
     */
    public function isSuccessful(): bool
    {
        return in_array($this->httpCode, self::SUCCESS_CODES, true);
    }

    /**
     * Return the raw response data array.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Return the HTTP status code.
     */
    public function getCode(): int
    {
        return $this->httpCode;
    }

    /**
     * Return a human-readable error message from the response, if any.
     *
     * Checks PayPal v2 error fields in order of specificity.
     */
    public function getMessage(): string
    {
        if (!empty($this->data['message'])) {
            return (string)$this->data['message'];
        }

        if (!empty($this->data['details'][0]['description'])) {
            return (string)$this->data['details'][0]['description'];
        }

        if (!empty($this->data['details'][0]['issue'])) {
            return (string)$this->data['details'][0]['issue'];
        }

        return '';
    }

    /**
     * Return the PayPal order ID as the transaction reference.
     */
    public function getTransactionReference(): string
    {
        return !empty($this->data['id']) ? (string)$this->data['id'] : '';
    }

    /**
     * Find and return the customer-facing approval URL from the links array.
     */
    public function getRedirectUrl(): ?string
    {
        if (empty($this->data['links']) || !is_array($this->data['links'])) {
            return null;
        }

        foreach ($this->data['links'] as $link) {
            if (!empty($link['rel']) && $link['rel'] === 'approve' && !empty($link['href'])) {
                return (string)$link['href'];
            }
        }

        return null;
    }
}
