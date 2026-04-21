<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Services\Notification;

use AmeliaBooking\Domain\Services\Notification\AbstractMailService;
use AmeliaBooking\Domain\Services\Notification\MailServiceInterface;

/**
 * Class WpMailService
 */
class WpMailService extends AbstractMailService implements MailServiceInterface
{
    /**
     * WpMailService constructor.
     *
     * @param        $from
     * @param        $fromName
     */
    public function __construct($from, $fromName, $replyTo)
    {
        parent::__construct($from, $fromName, $replyTo);
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
        $content = [
            'Content-Type: text/html; charset=UTF-8',
            'From: '  . $this->fromName . ' <' . $this->from . '>',
            'Reply-To: ' . (!empty($this->replyTo) ? $this->replyTo : $this->from)
        ];

        if ($bccEmails) {
            $content[] = 'Bcc:' . implode(', ', $bccEmails);
        }

        $attachmentsLocations = [];

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
                    $attachmentsLocations[] = $tmpFile;
                }
            }
        }

        wp_mail($to, $subject, $body, $content, $attachmentsLocations);
    }
}
