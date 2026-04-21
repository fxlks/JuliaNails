<?php

namespace AmeliaBooking\Application\Commands\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Booking\BookingFallbackService;
use AmeliaBooking\Application\Services\User\CustomerApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\BookingCancellationException;
use AmeliaBooking\Domain\Entity\Booking\Appointment\Appointment;
use AmeliaBooking\Domain\Entity\Booking\Appointment\CustomerBooking;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Services\Reservation\ReservationServiceInterface;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Domain\ValueObjects\String\Token;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\AppointmentRepository;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\CustomerBookingRepository;
use AmeliaBooking\Infrastructure\Repository\User\ProviderRepository;
use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;
use Slim\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;
use UnexpectedValueException;

/**
 * Class ApproveBookingRemotelyCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Appointment
 */
class ApproveBookingRemotelyCommandHandler extends CommandHandler
{
    /**
     * @var array
     */
    public $mandatoryFields = [
        'token',
    ];

    /**
     * @throws UnexpectedValueException
     * @throws ContainerException
     * @throws \InvalidArgumentException
     * @throws ContainerValueNotFoundException
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     * @throws AccessDeniedException
     * @throws NotFoundException
     * @throws BookingCancellationException
     */
    public function handle(ApproveBookingRemotelyCommand $command): CommandResult
    {
        $this->checkMandatoryFields($command);

        $result = new CommandResult();

        $type = Entities::APPOINTMENT;

        /** @var CustomerApplicationService $customerAS */
        $customerAS = $this->container->get('application.user.customer.service');

        /** @var CustomerBookingRepository $bookingRepository */
        $bookingRepository = $this->container->get('domain.booking.customerBooking.repository');

        /** @var AbstractUser $user */
        $user = $this->container->get('logged.in.user');

        /** @var CustomerBooking $booking */
        $booking = $bookingRepository->getById((int)$command->getArg('id'));

        /** @var SettingsService $settingsService */
        $settingsService = $this->container->get('domain.settings.service');

        $notificationSettings = $settingsService->getCategorySettings('notifications');

        if ($booking === null) {
            if (!empty($notificationSettings['approveErrorUrl'])) {
                $result->setUrl($notificationSettings['approveErrorUrl']);
                return $result;
            }

            return $result->setHtml(BookingFallbackService::getFallbackHtml('failed'));
        }

        $token = $bookingRepository->getToken((int)$command->getArg('id'));

        if (empty($token['token'])) {
            throw new AccessDeniedException('You are not allowed to update booking status');
        }

        $booking->setToken(new Token($token['token']));

        if (
            $booking->getStatus()->getValue() !== BookingStatus::WAITING &&
            !$customerAS->isCustomerBooking($booking, $user, $command->getField('token'))
        ) {
            throw new AccessDeniedException('You are not allowed to update booking status');
        }

        if ($booking->getStatus()->getValue() === BookingStatus::WAITING) {
            try {
                $this->validateWaitingListCapacity($booking);
            } catch (BookingCancellationException $e) {
                $appointmentSettings = $settingsService->getCategorySettings('appointments');
                $waitingListDeniedUrl = !empty($appointmentSettings['waitingListAppointments']['redirectUrlDenied']) ?
                    $appointmentSettings['waitingListAppointments']['redirectUrlDenied'] : '';

                if (!empty($waitingListDeniedUrl)) {
                    $result->setUrl($waitingListDeniedUrl);
                    return $result;
                }

                if (!empty($notificationSettings['approveErrorUrl'])) {
                    $result->setUrl($notificationSettings['approveErrorUrl']);
                    return $result;
                }

                return $result->setHtml(BookingFallbackService::getFallbackHtml('failed'));
            }
        }

        /** @var ReservationServiceInterface $reservationService */
        $reservationService = $this->container->get('application.reservation.service')->get($type);

        $status = BookingStatus::APPROVED;

        do_action('amelia_before_booking_approved_link', $booking ? $booking->toArray() : null);

        $bookingData = $reservationService->updateStatus($booking, $status);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully updated booking status');
        $result->setData(
            array_merge(
                $bookingData,
                [
                    'type'    => $type,
                    'status'  => $status,
                    'message' => BackendStrings::get('appointment_status_changed') . strtolower(BackendStrings::get($status))
                ]
            )
        );

        if ($notificationSettings['approveSuccessUrl'] && $result->getResult() === CommandResult::RESULT_SUCCESS) {
            $result->setUrl($notificationSettings['approveSuccessUrl']);

            do_action('amelia_after_booking_approved_link', $booking ? $booking->toArray() : null);
            return $result;
        }

        if ($notificationSettings['approveErrorUrl'] && $result->getResult() === CommandResult::RESULT_ERROR) {
            $result->setUrl($notificationSettings['approveErrorUrl']);

            return $result;
        }
        // No redirect URL defined - show fallback page
        if ($result->getResult() === CommandResult::RESULT_SUCCESS) {
            return $result->setHtml(BookingFallbackService::getFallbackHtml('approved'));
        }
        return $result->setHtml(BookingFallbackService::getFallbackHtml('approved_with_issues'));
    }

    /**
     * Validates waiting list appointment capacity
     *
     * @throws ContainerValueNotFoundException
     * @throws ContainerException
     * @throws BookingCancellationException
     * @throws NotFoundException
     */
    private function validateWaitingListCapacity(CustomerBooking $booking): void
    {
        /** @var AppointmentRepository $appointmentRepo */
        $appointmentRepo = $this->container->get('domain.booking.appointment.repository');

        /** @var Appointment $appointment */
        $appointment = $appointmentRepo->getById($booking->getAppointmentId()->getValue());

        if ($appointment === null) {
            throw new NotFoundException('This appointment does not exist!');
        }

        /** @var ProviderRepository $providerRepository */
        $providerRepository = $this->container->get('domain.users.providers.repository');

        $capacity = $providerRepository->getMaxCapacityByServiceId(
            $appointment->getProviderId()->getValue(),
            $appointment->getServiceId()->getValue()
        );

        $currentBookedPersons = $this->calculateCurrentBookedPersons($appointment);
        $availableCapacity = $capacity - $currentBookedPersons;

        if ($availableCapacity < $booking->getPersons()->getValue()) {
            throw new BookingCancellationException(
                'This slot is already taken! Available capacity: ' . $availableCapacity . ', requested: ' . $booking->getPersons()->getValue()
            );
        }
    }

    /**
     * Calculates the total number of persons currently booked for an appointment
     */
    private function calculateCurrentBookedPersons(Appointment $appointment): int
    {
        $persons = 0;
        $approvedStatuses = [BookingStatus::APPROVED, BookingStatus::PENDING];

        foreach ($appointment->getBookings()->getItems() as $existingBooking) {
            if (in_array($existingBooking->getStatus()->getValue(), $approvedStatuses)) {
                $persons += $existingBooking->getPersons()->getValue();
            }
        }

        return $persons;
    }
}
