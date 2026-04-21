<?php

namespace AmeliaBooking\Infrastructure\Services\Google;

use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaVendor\Google\Client;
use AmeliaVendor\Google\Service\Calendar;

class GoogleCalendarMiddlewareService extends AbstractGoogleCalendarMiddlewareService
{
    private const GOOGLE_USER_INFO_URL = 'https://www.googleapis.com/oauth2/v3/userinfo';
    private $settingsService;
    private $googleCalendarSettings;
    private $googleUserInfoUrl;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
        $this->googleCalendarSettings = $settingsService->getCategorySettings('googleCalendar');
        $this->googleUserInfoUrl = self::GOOGLE_USER_INFO_URL;
    }

    public function getAuthUrl($providerId, $returnUrl, $isBackend)
    {
        $ajaxUrl = admin_url('admin-ajax.php', '');
        $params = [
            'admin_ajax_url' => $ajaxUrl,
            'providerId' => $providerId,
            'returnUrl' => $returnUrl,
            'isBackend' => $isBackend,
        ];

        $url = AMELIA_MIDDLEWARE_URL . 'google/authorization/url?' . http_build_query($params);
        $ch = curl_init($url);

        // Check if curl initialization failed
        if ($ch === false) {
            error_log('GoogleCalendar: Failed to initialize curl for URL: ' . $url);
            return null;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        $url = null;
        if ($response) {
            $response = json_decode($response, true);
            $url      = $response['result'];
        }

        return $url;
    }

    /**
     * Refresh Google Access Token using the refresh token.
     *
     * @param string $refreshToken
     *
     * @return array|null
     */
    public function refreshAccessToken($refreshToken)
    {
        $endpoint = AMELIA_MIDDLEWARE_URL . 'google/authorization/refresh';

        $payload = json_encode([
            'refresh_token' => $refreshToken,
        ]);

        // Check if JSON encoding failed
        if ($payload === false) {
            error_log('GoogleCalendar: Failed to encode refresh token payload');
            return null;
        }

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode !== 200 || !$response) {
            return null; // or throw an exception
        }

        $data = json_decode($response, true);

        return $data['result'] ?? null;
    }

    /**
     * Get list of Google Calendars
     *
     * @param array $providerGoogleCalendar
     *
     * @return array
     */
    public function getCalendarList($providerGoogleCalendar)
    {
        $calendars = [];

        if ($this->isCalendarEnabled()) {
            $client = $this->getClient($providerGoogleCalendar);

            // Check if client creation failed
            if ($client === null) {
                error_log('GoogleCalendar: Failed to get Google Client in getCalendarList');
                return $calendars;
            }

            try {
                $service = new Calendar($client);

                $calendarList = $service->calendarList->listCalendarList(['minAccessRole' => 'writer']);

                foreach ($calendarList->getItems() as $calendar) {
                    $calendars[] = [
                        'id'      => $calendar->getId(),
                        'primary' => $calendar->getPrimary(),
                        'summary' => $calendar->getSummary()
                    ];
                }
            } catch (\Exception $e) {
                error_log('GoogleCalendar: Error fetching calendar list - ' . $e->getMessage());
            }
        }

        return $calendars;
    }

    /**
     * Get Google User Info (name, email, picture)
     *
     * @param string $accessToken
     *
     * @return array|null
     */
    public function getUserInfo($accessToken)
    {
        $url = $this->googleUserInfoUrl;

        $accessToken = json_decode($accessToken, true);

        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken['access_token'],
            ],
        ];

        $response = wp_remote_get($url, $args);

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return [
            'email'     => $data['email'] ?? null,
            'name'      => $data['name'] ?? null,
            'picture'   => $data['picture'] ?? null,
        ];
    }

    /**
     * Get Google Client
     * @param array|null $providerGoogleCalendar
     *
     * @return Client|null
     */
    public function getClient($providerGoogleCalendar)
    {
        $accessToken = $this->googleCalendarSettings['accessToken'];
        if ($accessToken === 'null') {
            $this->settingsService->setSetting('googleCalendar', 'accessToken', '');
            $this->googleCalendarSettings['accessToken'] = '';
            $accessToken = $this->googleCalendarSettings['accessToken'];
        }

        $isProviderToken = false;
        if (isset($providerGoogleCalendar['token'])) {
            $accessToken = $providerGoogleCalendar['token'];
            $isProviderToken = true;
        }

        if (!$accessToken) {
            error_log('GoogleCalendar: No access token available');
            return null;
        }

        $client = new Client();
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            $tokenData = json_decode($isProviderToken ? $providerGoogleCalendar['token'] : $this->googleCalendarSettings['accessToken'], true);
            $refreshToken = $tokenData['refresh_token'] ?? null;

            if (!$refreshToken) {
                error_log('GoogleCalendar: No refresh token available');
                return null;
            }

            $newAccessToken = $this->refreshAccessToken($refreshToken);

            if ($newAccessToken === null) {
                error_log('GoogleCalendar: Failed to refresh access token');
                return null;
            }

            if (empty($newAccessToken['refresh_token'])) {
                $newAccessToken['refresh_token'] = $refreshToken;
            }

            $token = json_encode($newAccessToken);

            if (!$isProviderToken) {
                $this->googleCalendarSettings['accessToken'] = $token;
                $this->settingsService->setCategorySettings('googleCalendar', $this->googleCalendarSettings);
            }

            $client->setAccessToken($token);
        }

        return $client;
    }

    private function isCalendarEnabled()
    {
        return $this->settingsService->isFeatureEnabled('googleCalendar') &&
            $this->googleCalendarSettings['accessToken'];
    }
}
