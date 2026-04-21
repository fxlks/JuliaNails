<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\EventListeners\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Services\Booking\BookingApplicationService;
use AmeliaBooking\Application\Services\Booking\IcsApplicationService;
use AmeliaBooking\Application\Services\Integration\ApplicationIntegrationService;
use AmeliaBooking\Application\Services\Notification\ApplicationNotificationService;
use AmeliaBooking\Application\Services\WebHook\AbstractWebHookApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Appointment\Appointment;
use AmeliaBooking\Domain\Entity\Booking\Event\Event;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Factory\Booking\Appointment\AppointmentFactory;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use Exception;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class AppointmentStatusUpdatedEventHandler
 *
 * @package AmeliaBooking\Infrastructure\WP\EventListeners\Booking\Appointment
 */
class AppointmentStatusUpdatedEventHandler
{
    /** @var string */
    public const APPOINTMENT_STATUS_UPDATED = 'appointmentStatusUpdated';

    /** @var string */
    public const BOOKING_STATUS_UPDATED = 'bookingStatusUpdated';

    /**
     * @param CommandResult $commandResult
     * @param Container     $container
     *
     * @throws ContainerValueNotFoundException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws Exception
     */
    public static function handle($commandResult, $container)
    {
        /** @var ApplicationNotificationService $applicationNotificationService */
        $applicationNotificationService = $container->get('application.notification.service');
        /** @var ApplicationIntegrationService $applicationIntegrationService */
        $applicationIntegrationService = $container->get('application.integration.service');
        /** @var AbstractWebHookApplicationService $webHookService */
        $webHookService = $container->get('application.webHook.service');
        /** @var BookingApplicationService $bookingApplicationService */
        $bookingApplicationService = $container->get('application.booking.booking.service');

        $appointment = $commandResult->getData()[Entities::APPOINTMENT];

        /** @var Appointment|Event $reservationObject */
        $reservationObject = AppointmentFactory::create($appointment);

        $bookingApplicationService->setReservationEntities($reservationObject);

        $applicationIntegrationService->handleAppointment(
            $reservationObject,
            $appointment,
            ApplicationIntegrationService::APPOINTMENT_STATUS_UPDATED,
            [
                ApplicationIntegrationService::SKIP_GOOGLE_CALENDAR  => $appointment['status'] === BookingStatus::NO_SHOW,
                ApplicationIntegrationService::SKIP_OUTLOOK_CALENDAR => $appointment['status'] === BookingStatus::NO_SHOW,
                ApplicationIntegrationService::SKIP_APPLE_CALENDAR   => $appointment['status'] === BookingStatus::NO_SHOW,
            ]
        );

        $bookings = $commandResult->getData()['bookingsWithChangedStatus'];

        // if appointment approved add ics file to bookings
        if ($appointment['status'] === BookingStatus::APPROVED || $appointment['status'] === BookingStatus::PENDING) {
            /** @var IcsApplicationService $icsService */
            $icsService = $container->get('application.ics.service');

            foreach ($appointment['bookings'] as $index => $booking) {
                if ($appointment['bookings'][$index]['isChangedStatus'] === true) {
                    $appointment['bookings'][$index]['icsFiles'] = $icsService->getIcsData(
                        Entities::APPOINTMENT,
                        $booking['id'],
                        [],
                        true
                    );
                }
            }
        }

        $applicationNotificationService->sendAppointmentProviderStatusNotifications(
            $reservationObject
        );

        $applicationNotificationService->sendAppointmentCustomersStatusNotifications(
            $reservationObject,
            $reservationObject->getBookings()
        );

        if ($bookings) {
            if ($appointment['status'] === BookingStatus::CANCELED) {
                $webHookService->process(BookingCanceledEventHandler::BOOKING_CANCELED, $appointment, $bookings);
            } else {
                $webHookService->process(self::BOOKING_STATUS_UPDATED, $appointment, $bookings);
            }
        }
    }
}
