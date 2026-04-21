<?php

namespace AmeliaBooking\Infrastructure\Services\Outlook;

use AmeliaBooking\Infrastructure\Common\Container;

abstract class AbstractOutlookCalendarMiddlewareService
{
    /** @var Container $container */
    protected $container;

    /**
     * Create a URL to obtain user authorization.
     *
     * @param int|null $providerId
     * @param string|null $returnUrl
     * @param bool $isBackend
     * @return string|null
     */
    abstract public function getAuthUrl(?int $providerId, ?string $returnUrl, bool $isBackend): ?string;

    /**
     * Get list of Outlook Calendars
     *
     * @param array $providerOutlookCalendar
     *
     * @return array
     */
    abstract public function getCalendarList(array $providerOutlookCalendar): array;

    /**
     * @param array|null $provider
     * @return void
     */
    abstract public function authorizeProvider(array $provider = null): void;

    /**
     * Get configured Graph client with the appropriate token (provider-specific or global)
     *
     * @param array|null $providerOutlookCalendar
     * @return mixed
     */
    abstract public function getGraph(array $providerOutlookCalendar = null);

    /**
     * Get Outlook User Info (name, email)
     *
     * @param string $accessToken
     *
     * @return array
     */
    abstract public function getUserInfo(string $accessToken): array;

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param string $from
     * @param string $fromName
     * @param string $replyTo
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param array $bccEmails
     * @param array $attachments
     *
     * @return void
     */
    abstract public function sendEmail(
        string $from,
        string $fromName,
        string $replyTo,
        string $to,
        string $subject,
        string $body,
        array $bccEmails = [],
        array $attachments = []
    );
}
