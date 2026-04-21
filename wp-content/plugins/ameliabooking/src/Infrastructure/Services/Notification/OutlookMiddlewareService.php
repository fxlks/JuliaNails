<?php

namespace AmeliaBooking\Infrastructure\Services\Notification;

use AmeliaBooking\Domain\Services\Notification\AbstractMailService;
use AmeliaBooking\Domain\Services\Notification\MailServiceInterface;
use AmeliaBooking\Infrastructure\Services\Outlook\AbstractOutlookCalendarMiddlewareService;
use Exception;

/**
 * Class OutlookMiddlewareService
 */
class OutlookMiddlewareService extends AbstractMailService implements MailServiceInterface
{
    /** @var AbstractOutlookCalendarMiddlewareService */
    private $outlookCalendarMiddlewareService;

    /**
     * OutlookMiddlewareService constructor.
     *
     * @param AbstractOutlookCalendarMiddlewareService $outlookCalendarMiddlewareService
     * @param string $from
     * @param string $fromName
     * @param string $replyTo
     */
    public function __construct($outlookCalendarMiddlewareService, $from, $fromName, $replyTo)
    {
        $this->outlookCalendarMiddlewareService = $outlookCalendarMiddlewareService;

        parent::__construct($from, $fromName, $replyTo);
    }

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param array  $bcc
     * @param array  $attachments
     *
     * @return void
     * @throws Exception
     * @SuppressWarnings(PHPMD)
     */
    public function send($to, $subject, $body, $bcc = [], $attachments = [])
    {
        $attachmentsList = [];

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
                    $attachmentsList[] = ['filePath' => $tmpFile, 'fileName' => $attachment['name']];
                }
            }
        }

        $this->outlookCalendarMiddlewareService->sendEmail(
            $this->from,
            $this->fromName,
            $this->replyTo,
            $to,
            $subject,
            $body,
            $bcc,
            $attachmentsList
        );

        // Clean up temporary attachment files
        foreach ($attachmentsList as $attachment) {
            if (!empty($attachment['filePath']) && file_exists($attachment['filePath'])) {
                @unlink($attachment['filePath']);
            }
        }
    }
}
