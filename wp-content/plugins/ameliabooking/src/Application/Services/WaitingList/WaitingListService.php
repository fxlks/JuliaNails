<?php

namespace AmeliaBooking\Application\Services\WaitingList;

use AmeliaBooking\Application\Services\Notification\ApplicationNotificationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Entity\Booking\Appointment\Appointment;
use AmeliaBooking\Domain\Entity\Bookable\Service\Service;
use AmeliaBooking\Domain\Entity\Booking\Appointment\CustomerBooking;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\AppointmentRepository;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class WaitingListService
 *
 * Encapsulates logic for determining whether a booking should be treated as a
 * waiting list booking (and therefore skip the regular slot availability check).
 */
class WaitingListService
{
    /** @var Container */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Notify waiting-list customers that a spot is available for the given appointment.
     *
     * Builds the waiting bookings collection from the appointment entity and
     * dispatches notifications through all enabled channels.
     *
     * @param Appointment $appointment
     *
     * @throws ContainerException
     * @throws ContainerValueNotFoundException
     * @throws QueryExecutionException
     */
    public function sendAvailableSpotNotifications($appointment)
    {
        $waitingBookings = new Collection();

        foreach ($appointment->getBookings()->getItems() as $booking) {
            if ($booking->getStatus()->getValue() === BookingStatus::WAITING) {
                $waitingBookings->addItem($booking);
            }
        }

        if ($waitingBookings->length()) {
            /** @var ApplicationNotificationService $applicationNotificationService */
            $applicationNotificationService = $this->container->get('application.notification.service');

            $applicationNotificationService->sendWaitingListAvailableSpotNotifications(
                $appointment,
                $waitingBookings
            );
        }
    }

    /**
    * Determine if current booking qualifies as a waiting list booking.
     *
     * @param Service         $service
     * @param array           $appointmentData (expects bookingStart, serviceId, providerId, bookings array)
     * @param CustomerBooking $booking
     *
     * @return bool  True if booking is legitimate waiting list booking (skip slot check), false otherwise.
     * @throws ContainerValueNotFoundException
     * @throws ContainerException
     * @throws QueryExecutionException
     */
    public function isWaitingListBooking($service, $appointmentData, $booking)
    {
        if (empty($appointmentData['bookings'][0]['status']) || $appointmentData['bookings'][0]['status'] !== BookingStatus::WAITING) {
            return false;
        }

        // Extract waiting list settings from service settings JSON
        $rawSettings = null;
        if ($service->getSettings()) {
            $rawSettings = $service->getSettings()->getValue() ?? $service->getSettings();
        }

        $decoded = [];
        if (is_string($rawSettings)) {
            $decoded = json_decode($rawSettings, true) ?: [];
        } elseif (is_array($rawSettings)) {
            $decoded = $rawSettings;
        }

        $waitingSettings = $decoded['waitingList'] ?? [];
        $waitingEnabled = !empty($waitingSettings['enabled']);
        $waitingCapacity = isset($waitingSettings['maxCapacity']) ? (int)$waitingSettings['maxCapacity'] : 0;

        if (!$waitingEnabled || $waitingCapacity <= 0) {
            return false; // feature not enabled / no capacity defined
        }

        // Retrieve existing appointment(s) at same slot
        /** @var AppointmentRepository $appointmentRepo */
        $appointmentRepo = $this->container->get('domain.booking.appointment.repository');

        $existingAppointments = $appointmentRepo->getFiltered([
            'dates'         => [$appointmentData['bookingStart'], $appointmentData['bookingStart']],
            'services'      => [$appointmentData['serviceId']],
            'providers'     => [$appointmentData['providerId']],
            'skipServices'  => true,
            'skipProviders' => true,
            'skipCustomers' => true,
        ]);

        if (!$existingAppointments->length()) {
            return false;
        }

        $currentWaitingPersons = 0;
        foreach ($existingAppointments->getItems() as $existingAppointment) {
            foreach ($existingAppointment->getBookings()->getItems() as $existingBooking) {
                if ($existingBooking->getStatus()->getValue() === BookingStatus::WAITING) {
                    $currentWaitingPersons += $existingBooking->getPersons()->getValue();
                }
            }
        }

        $newPersons = $booking->getPersons() ? $booking->getPersons()->getValue() : 1;

        if ($currentWaitingPersons + $newPersons <= $waitingCapacity) {
            return true;
        }

        return false;
    }
}
