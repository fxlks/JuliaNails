<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Domain\Services\Notification;

/**
 * Interface MailServiceInterface
 *
 * @package AmeliaBooking\Domain\Services\Notification
 */
interface MailServiceInterface
{
    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param       $to
     * @param       $subject
     * @param       $body
     * @param array $bcc
     *
     * @return mixed
     * @SuppressWarnings(PHPMD)
     */
    public function send($to, $subject, $body, $bcc = []);
}
