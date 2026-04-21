<?php

namespace AmeliaBooking\Application\Commands\Notification;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Notification\NotificationSMSHistoryRepository;

/**
 * Class UpdateSMSNotificationHistoryDirectlyCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Notification
 */
class UpdateSMSNotificationHistoryDirectlyCommandHandler extends CommandHandler
{
    /**
     * @param UpdateSMSNotificationHistoryDirectlyCommand $command
     *
     * @return CommandResult
     * @throws QueryExecutionException
     * @throws AccessDeniedException
     */
    public function handle(UpdateSMSNotificationHistoryDirectlyCommand $command)
    {
        if (!$this->getContainer()->getPermissionsService()->currentUserCanWrite('notifications')) {
            throw new AccessDeniedException('You are not allowed to update sms notifications.');
        }

        $result = new CommandResult();

        /** @var NotificationSMSHistoryRepository $notificationsSMSHistoryRepo */
        $notificationsSMSHistoryRepo = $this->container->get('domain.notificationSMSHistory.repository');

        $updateData = [
            'status'   => $command->getField('status'),
            'price'    => $command->getField('price'),
            'logId'    => $command->getField('logId'),
            'dateTime' => $command->getField('dateTime')
        ];

        $updateData = apply_filters('amelia_before_sms_notification_history_updated_filter', $updateData, $command->getArg('id'));

        do_action('amelia_before_sms_notification_history_updated', $updateData, $command->getArg('id'));

        if ($notificationsSMSHistoryRepo->update((int)$command->getArg('id'), $updateData)) {
            do_action('amelia_after_sms_notification_history_updated', $updateData, $command->getArg('id'));

            $result->setResult(CommandResult::RESULT_SUCCESS);
            $result->setMessage('Successfully updated SMS notification history.');
        }

        return $result;
    }
}
