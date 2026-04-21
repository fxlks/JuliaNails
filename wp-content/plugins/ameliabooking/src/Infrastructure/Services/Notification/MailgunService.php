<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Services\Notification;

use AmeliaBooking\Domain\Services\Notification\AbstractMailService;
use AmeliaBooking\Domain\Services\Notification\MailServiceInterface;

/**
 * Class MailgunService
 */
class MailgunService extends AbstractMailService implements MailServiceInterface
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $domain;

    /** @var string */
    private $endpoint;

    /**
     * MailgunService constructor.
     *
     * @param string $from
     * @param string $fromName
     * @param string $apiKey
     * @param string $domain
     * @param string $endpoint
     */
    public function __construct($from, $fromName, $apiKey, $domain, $endpoint, $replyTo)
    {
        parent::__construct($from, $fromName, $replyTo);
        $this->apiKey   = $apiKey;
        $this->domain   = $domain;
        $this->endpoint = $endpoint;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param       $to
     * @param       $subject
     * @param       $body
     * @param array $bccEmails
     * @param array $attachments
     *
     * @return mixed|void
     * @SuppressWarnings(PHPMD)
     */
    public function send($to, $subject, $body, $bccEmails = [], $attachments = [])
    {
        $mgArgs = [
            'from'       => "{$this->fromName} <{$this->from}>",
            'to'         => $to,
            'subject'    => $subject,
            'html'       => $body,
            'h:Reply-To' => !empty($this->replyTo) ? $this->replyTo : $this->from
        ];

        if ($bccEmails) {
            $mgArgs['bcc'] = implode(', ', $bccEmails);
        }

        $attachmentIndex = 0;
        foreach ($attachments as $attachment) {
            if (!empty($attachment['content'])) {
                $extension = pathinfo($attachment['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($attachment['name'], PATHINFO_FILENAME);
                $tmpFile = tempnam(sys_get_temp_dir(), $fileName . '_');
                if (
                    $tmpFile &&
                    file_put_contents($tmpFile, $attachment['content']) !== false &&
                    @rename($tmpFile, $tmpFile .= '.' . $extension) !== false
                ) {
                    $mgArgs["attachment[$attachmentIndex]"] = new \CURLFile($tmpFile, mime_content_type($tmpFile), $attachment['name']);
                    $attachmentIndex++;
                }
            }
        }

        $endpoint = $this->endpoint ?: 'https://api.mailgun.net/';

        $url = $endpoint . "v3/{$this->domain}/messages";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $mgArgs);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new \RuntimeException('Mailgun error: ' . curl_error($ch));
        }
    }
}
