<?php

namespace AmeliaBooking\Infrastructure\Services\Outlook;

use AmeliaBooking\Infrastructure\Common\Container;

class StarterOutlookCalendarMiddlewareService extends AbstractOutlookCalendarMiddlewareService
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getAuthUrl(?int $providerId, ?string $returnUrl, bool $isBackend): ?string
    {
        return '';
    }

    public function authorizeProvider(array $provider = null): void
    {
    }

    public function getGraph(array $providerOutlookCalendar = null)
    {
        return null;
    }

    public function getCalendarList(array $providerOutlookCalendar): array
    {
        return [];
    }

    public function getUserInfo(string $accessToken): array
    {
        return [];
    }

    public function sendEmail(
        string $from,
        string $fromName,
        string $replyTo,
        string $to,
        string $subject,
        string $body,
        array $bccEmails = [],
        array $attachments = []
    ): void {
    }
}
