<?php

namespace AmeliaBooking\Infrastructure\Services\Google;

use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaVendor\Google\Client;

class StarterGoogleCalendarMiddlewareService extends AbstractGoogleCalendarMiddlewareService
{
    /**
     * StarterGoogleCalendarMiddlewareService constructor.
     *
     * @param Container $container
     *
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Create a URL to obtain user authorization.
     *
     * @param $providerId
     * @param $returnUrl
     * @param $isBackend
     * @return string
     */
    public function getAuthUrl($providerId, $returnUrl, $isBackend)
    {
        return '';
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
        return null;
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
        return [];
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
        return null;
    }

    /**
     * Get Google Client
     * $providerGoogleCalendar - provider's google calendar settings
     * @param array $providerGoogleCalendar
     *
     * @return Client|null
     */
    public function getClient($providerGoogleCalendar)
    {
        return null;
    }
}
