<?php

namespace AmeliaBooking\Infrastructure\Services\Google;

use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaVendor\Google\Client;

/**
 * Class AbstractGoogleCalendarMiddlewareService
 *
 * @package AmeliaBooking\Infrastructure\Services\Google
 */
abstract class AbstractGoogleCalendarMiddlewareService
{
    /** @var Container $container */
    protected $container;

    /**
     * Create a URL to obtain user authorization.
     *
     * @param $providerId
     * @param $returnUrl
     * @param $isBackend
     * @return string|null
     */
    abstract public function getAuthUrl($providerId, $returnUrl, $isBackend);

    /**
     * Refresh Google Access Token using the refresh token.
     *
     * @param string $refreshToken
     *
     * @return array|null
     */
    abstract public function refreshAccessToken($refreshToken);

    /**
     * Get list of Google Calendars where user has at least 'writer' access.
     *
     * @param array $providerGoogleCalendar
     *
     * @return array
     */
    abstract public function getCalendarList($providerGoogleCalendar);

    /**
     * Get Google User Info (name, email, picture)
     *
     * @param string $accessToken
     *
     * @return array|null
     */
    abstract public function getUserInfo($accessToken);

    /**
     * Get Google Client
     * $providerGoogleCalendar - provider's google calendar settings
     * @param array $providerGoogleCalendar
     *
     * @return Client|null
     */
    abstract public function getClient($providerGoogleCalendar);
}
