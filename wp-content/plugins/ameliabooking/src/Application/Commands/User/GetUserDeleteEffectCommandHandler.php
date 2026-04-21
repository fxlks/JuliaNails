<?php

namespace AmeliaBooking\Application\Commands\User;

use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\User\UserApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Event\EventRepository;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class GetUserDeleteEffectCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\User
 */
class GetUserDeleteEffectCommandHandler extends CommandHandler
{
    /**
     * @param GetUserDeleteEffectCommand $command
     *
     * @return CommandResult
     * @throws ContainerValueNotFoundException
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws ContainerException
     */
    public function handle(GetUserDeleteEffectCommand $command)
    {
        if (
            !$command->getPermissionService()->currentUserCanRead(Entities::EMPLOYEES) &&
            !$command->getPermissionService()->currentUserCanRead(Entities::CUSTOMERS)
        ) {
            throw new AccessDeniedException('You are not allowed to read user');
        }

        $result = new CommandResult();

        /** @var UserApplicationService $userAS */
        $userAS = $this->getContainer()->get('application.user.service');

        /** @var EventRepository $eventRepository */
        $eventRepository = $this->container->get('domain.booking.event.repository');

        $appointmentsCount = $userAS->getAppointmentsCountForUser($command->getArg('id'));

        $eventsIds = $eventRepository->getFilteredIds(
            [
                'customerId'            => $command->getArg('id'),
                'customerBookingStatus' => BookingStatus::APPROVED,
                'dates'                 => [DateTimeService::getNowDateTime()],
            ],
            0
        );

        $messageKey = '';
        $messageData = null;

        if ($appointmentsCount['futureAppointments'] > 0) {
            $messageKey = 'red_delete_user_effect_future';
            $messageData = ['count' => $appointmentsCount['futureAppointments']];
        } elseif ($appointmentsCount['packageAppointments']) {
            $messageKey = 'red_delete_user_effect_package';
        } elseif ($appointmentsCount['pastAppointments'] > 0) {
            $messageKey = 'red_delete_user_effect_past';
            $messageData = ['count' => $appointmentsCount['pastAppointments']];
        } elseif ($eventsIds) {
            $messageKey = 'red_delete_user_event_effect_future';
            $messageData = ['count' => count($eventsIds)];
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved message.');
        $result->setData(
            [
                'valid'                    => ! $appointmentsCount['futureAppointments'],
                'messageKey'               => $messageKey,
                'messageData'              => $messageData,
                'futureAppointmentsCount'  => $appointmentsCount['futureAppointments'],
                'packageAppointmentsCount' => $appointmentsCount['packageAppointments'],
                'pastAppointmentsCount'    => $appointmentsCount['pastAppointments'],
                'eventsCount'              => count($eventsIds)
            ]
        );

        return $result;
    }
}
