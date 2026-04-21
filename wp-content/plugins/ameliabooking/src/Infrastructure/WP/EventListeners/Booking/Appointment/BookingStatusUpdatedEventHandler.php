<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\EventListeners\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Services\Booking\BookingApplicationService;
use AmeliaBooking\Application\Services\Integration\ApplicationIntegrationService;
use AmeliaBooking\Application\Services\Notification\ApplicationNotificationService;
use AmeliaBooking\Application\Services\WaitingList\WaitingListService;
use AmeliaBooking\Application\Services\WebHook\AbstractWebHookApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Appointment\Appointment;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Factory\Booking\Appointment\AppointmentFactory;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use Exception;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class BookingStatusUpdatedEventHandler
 *
 * @package AmeliaBooking\Infrastructure\WP\EventListeners\Booking\Appointment
 */
class BookingStatusUpdatedEventHandler
{
    /** @var string */
    public const BOOKING_STATUS_UPDATED = 'bookingStatusUpdated';

    /** @var string */
    public const BOOKING_CANCELED = 'bookingCanceled';

    /**
     * @param CommandResult $commandResult
     * @param Container     $container
     *
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws ContainerValueNotFoundException
     * @throws QueryExecutionException
     * @throws ContainerException
     * @throws Exception
     */
    public static function handle($commandResult, $container)
    {
        /** @var ApplicationIntegrationService $applicationIntegrationService */
        $applicationIntegrationService = $container->get('application.integration.service');

        /** @var ApplicationNotificationService $applicationNotificationService */
        $applicationNotificationService = $container->get('application.notification.service');

        /** @var AbstractWebHookApplicationService $webHookService */
        $webHookService = $container->get('application.webHook.service');

        /** @var BookingApplicationService $bookingApplicationService */
        $bookingApplicationService = $container->get('application.booking.booking.service');

        $appointmentArray = $commandResult->getData()[$commandResult->getData()['type']];

        $appointmentStatusChanged = $commandResult->getData()['appointmentStatusChanged'];

        /** @var Appointment $appointment */
        $appointment = AppointmentFactory::create($appointmentArray);

        $bookingApplicationService->setReservationEntities($appointment);

        $applicationIntegrationService->handleAppointment(
            $appointment,
            $appointmentArray,
            self::BOOKING_STATUS_UPDATED,
            [
                ApplicationIntegrationService::SKIP_LESSON_SPACE => true,
            ]
        );

        $booking = $commandResult->getData()[Entities::BOOKING];

        $payments = $appointmentArray['bookings'][0]['payments'];

        if ($payments && count($payments)) {
            $booking['payments'] = $payments;
        }

        if ($appointmentStatusChanged) {
            $applicationNotificationService->sendAppointmentProviderStatusNotifications(
                $appointment
            );
        }

        $applicationNotificationService->sendAppointmentCustomersStatusNotifications(
            $appointment,
            $appointment->getBookings(),
            true,
            true,
            false
        );

        if (!$appointmentStatusChanged && $appointment->getStatus()->getValue() === BookingStatus::APPROVED) {
            $applicationNotificationService->sendAppointmentUpdatedNotifications(
                $appointment,
                null,
                true,
                true
            );
        }

        if (!empty($booking['status']) && in_array($booking['status'], [BookingStatus::CANCELED, BookingStatus::REJECTED], true)) {
            /** @var WaitingListService $waitingListService */
            $waitingListService = $container->get('application.waitingList.service');
            $waitingListService->sendAvailableSpotNotifications($appointment);
        }

        if ($booking['status'] === BookingStatus::CANCELED) {
            $webHookService->process(self::BOOKING_CANCELED, $appointmentArray, [$booking]);
        } else {
            $webHookService->process(self::BOOKING_STATUS_UPDATED, $appointmentArray, [$booking]);
        }
    }
}
