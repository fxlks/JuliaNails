<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Services\PayPal;

use Exception;

/**
 * Class PayPalClient
 *
 * Native cURL-based PayPal REST API v2 client with two-tier OAuth token caching.
 */
class PayPalClient
{
    private const SANDBOX_API_BASE = 'https://api-m.sandbox.paypal.com';
    private const LIVE_API_BASE    = 'https://api-m.paypal.com';

    /**
     * @var string
     */
    private string $clientId;

    /**
     * @var string
     */
    private string $secret;

    /**
     * @var bool
     */
    private bool $testMode;

    /**
     * @var string
     */
    private string $apiBase;

    /**
     * @var string|null
     */
    private ?string $accessToken = null;

    /**
     * @var int|null
     */
    private ?int $tokenExpiry = null;

    /**
     * PayPalClient constructor.
     */
    public function __construct(string $clientId, string $secret, bool $testMode = true)
    {
        $this->clientId = $clientId;
        $this->secret   = $secret;
        $this->testMode = $testMode;
        $this->apiBase  = $testMode ? self::SANDBOX_API_BASE : self::LIVE_API_BASE;
    }

    /**
     * Create PayPal order
     *
     * @throws Exception
     */
    public function createOrder(array $data): array
    {
        $payload = [
            'intent'              => 'CAPTURE',
            'purchase_units'      => [
                [
                    'amount'      => [
                        'currency_code' => $data['currency'],
                        'value'         => number_format((float)$data['amount'], 2, '.', ''),
                    ],
                    'description' => !empty($data['description'])
                        ? substr($data['description'], 0, 127)
                        : 'Payment',
                ],
            ],
            'application_context' => [
                'return_url'  => $data['returnUrl'],
                'cancel_url'  => $data['cancelUrl'],
                'user_action' => 'PAY_NOW',
            ],
        ];

        return $this->request('POST', '/v2/checkout/orders', $payload);
    }

    /**
     * Capture a PayPal order
     *
     * @throws Exception
     */
    public function captureOrder(string $orderId): array
    {
        if (empty($orderId)) {
            throw new Exception('Order ID is required');
        }

        return $this->request('POST', '/v2/checkout/orders/' . urlencode($orderId) . '/capture', []);
    }

    /**
     * Get order details
     *
     * @throws Exception
     */
    public function getOrder(string $orderId): array
    {
        if (empty($orderId)) {
            throw new Exception('Order ID is required');
        }

        return $this->request('GET', '/v2/checkout/orders/' . urlencode($orderId));
    }

    /**
     * Refund a captured payment (full or partial).
     *
     * @throws Exception
     */
    public function refundCapture(string $captureId, array $data = []): array
    {
        if (empty($captureId)) {
            throw new Exception('Capture ID is required');
        }

        $payload = [];

        if (!empty($data['amount'])) {
            $payload['amount'] = [
                'currency_code' => !empty($data['currency']) ? $data['currency'] : 'USD',
                'value'         => number_format((float)$data['amount'], 2, '.', ''),
            ];
        }

        return $this->request('POST', '/v2/payments/captures/' . urlencode($captureId) . '/refund', $payload);
    }

    /**
     * Get or refresh the OAuth 2.0 access token.
     *
     * Uses a two-tier cache: in-memory for the current request lifetime, and a
     * WordPress transient for cross-request caching (~1-hour TTL), reducing
     * repeated auth round-trips to a single call per payment cycle.
     *
     * @throws Exception
     */
    private function getAccessToken(): string
    {
        if ($this->accessToken !== null && $this->tokenExpiry !== null && time() < $this->tokenExpiry) {
            return $this->accessToken;
        }

        $cacheKey = 'amelia_paypal_token_' . md5($this->clientId . ($this->testMode ? '_sb' : '_live'));
        $cached   = get_transient($cacheKey);

        if (
            $cached !== false &&
            !empty($cached['token']) &&
            !empty($cached['expiry']) &&
            time() < (int)$cached['expiry']
        ) {
            $this->accessToken = $cached['token'];
            $this->tokenExpiry = (int)$cached['expiry'];

            return $this->accessToken;
        }

        $response = $this->request('POST', '/v1/oauth2/token', ['grant_type' => 'client_credentials'], true);

        if (empty($response['access_token'])) {
            throw new Exception('Failed to obtain PayPal access token');
        }

        $expiresIn = isset($response['expires_in']) ? (int)$response['expires_in'] : 3600;

        $this->accessToken = $response['access_token'];
        $this->tokenExpiry = time() + $expiresIn - 60;

        set_transient(
            $cacheKey,
            ['token' => $this->accessToken, 'expiry' => $this->tokenExpiry],
            $expiresIn - 120
        );

        return $this->accessToken;
    }

    /**
     * Execute an HTTP request against the PayPal REST API.
     *
     * @throws Exception
     */
    private function request(string $method, string $endpoint, ?array $data = null, bool $isAuthRequest = false): array
    {
        $url = $this->apiBase . $endpoint;

        if ($isAuthRequest) {
            $headers = [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ];
        } else {
            $headers = [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $this->getAccessToken(),
            ];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($isAuthRequest) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->clientId . ':' . $this->secret);
        }

        if (in_array($method, ['POST', 'PATCH'], true)) {
            if ($isAuthRequest) {
                $body = http_build_query($data ?? []);
            } else {
                $payload = !empty($data) ? $data : new \stdClass();
                $body    = json_encode($payload);
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $rawResponse = curl_exec($ch);
        $httpCode    = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError   = curl_error($ch);

        if ($curlError) {
            throw new Exception('PayPal API cURL error: ' . $curlError);
        }

        $decoded = json_decode($rawResponse, true);

        if (!is_array($decoded)) {
            throw new Exception('Invalid PayPal API response (HTTP ' . $httpCode . '): ' . substr((string)$rawResponse, 0, 200));
        }

        $decoded['_http_code'] = $httpCode;

        return $decoded;
    }
}
