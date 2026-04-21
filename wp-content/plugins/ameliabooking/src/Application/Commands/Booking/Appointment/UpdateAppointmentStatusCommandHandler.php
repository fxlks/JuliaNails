<?php

namespace AmeliaBooking\Application\Commands\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Bookable\BookableApplicationService;
use AmeliaBooking\Application\Services\Booking\AppointmentApplicationService;
use AmeliaBooking\Application\Services\Booking\BookingApplicationService;
use AmeliaBooking\Application\Services\User\UserApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\AuthorizationException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\Service\Service;
use AmeliaBooking\Domain\Entity\Booking\Appointment\Appointment;
use AmeliaBooking\Domain\Entity\Booking\Appointment\CustomerBooking;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\ValueObjects\BooleanValueObject;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\AppointmentRepository;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\CustomerBookingRepository;
use AmeliaBooking\Infrastructure\Repository\User\ProviderRepository;
use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;
use AmeliaBooking\Infrastructure\WP\Translations\FrontendStrings;
use Interop\Container\Exception\ContainerException;

/**
 * Class UpdateAppointmentStatusCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Appointment
 */
class UpdateAppointmentStatusCommandHandler extends CommandHandler
{
    /**
     * @var array
     */
    public $mandatoryFields = [
        'status'
    ];

    /**
     * @param UpdateAppointmentStatusCommand $command
     *
     * @return CommandResult
     *
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function handle(UpdateAppointmentStatusCommand $command)
    {
        $result = new CommandResult();

        if (!$command->getPermissionService()->currentUserCanWriteStatus(Entities::APPOINTMENTS)) {
            try {
                /** @var AbstractUser $user */
                $user = $command->getUserApplicationService()->authorization(
                    $command->getPage() === 'cabinet' ? $command->getToken() : null,
                    $command->getCabinetType()
                );
            } catch (AuthorizationException $e) {
                $result->setResult(CommandResult::RESULT_ERROR);
                $result->setData(
                    [
                        'reauthorize' => true
                    ]
                );

                return $result;
            }
        } else {
            /** @var AbstractUser $user */
            $user = $this->container->get('logged.in.user');
        }

        $this->checkMandatoryFields($command);

        /** @var CustomerBookingRepository $bookingRepository */
        $bookingRepository = $this->container->get('domain.booking.customerBooking.repository');
        /** @var AppointmentRepository $appointmentRepo */
        $appointmentRepo = $this->container->get('domain.booking.appointment.repository');
        /** @var BookingApplicationService $bookingAS */
        $bookingAS = $this->container->get('application.booking.booking.service');
        /** @var UserApplicationService $userAS */
        $userAS = $command->getUserApplicationService();
        /** @var AppointmentApplicationService $appointmentAS */
        $appointmentAS = $this->container->get('application.booking.appointment.service');
        /** @var BookableApplicationService $bookableAS */
        $bookableAS = $this->container->get('application.bookable.service');
        /** @var ProviderRepository $providerRepo */
        $providerRepo = $this->container->get('domain.users.providers.repository');

        $appointmentId   = (int)$command->getArg('id');
        $requestedStatus = $command->getField('status');

        /** @var Appointment $appointment */
        $appointment = $appointmentRepo->getById($appointmentId);

        if ($userAS->isCustomer($user)) {
            /** @var CustomerBooking $booking */
            foreach ($appointment->getBookings()->getItems() as $booking) {
                if (
                    $booking->getCustomerId()->getValue() !== $user->getId()->getValue() &&
                    !$bookingAS->isBookingCanceledOrRejectedOrNoShow($booking->getStatus()->getValue())
                ) {
                    throw new AccessDeniedException('You are not allowed to update appointment');
                }
            }
        }

        $oldStatus = $appointment->getStatus()->getValue();

        if (
            $bookingAS->isBookingApprovedOrPending($requestedStatus) &&
            $bookingAS->isBookingCanceledOrRejectedOrNoShow($appointment->getStatus()->getValue()) &&
            !$appointmentAS->canBeBooked($appointment, $userAS->isCustomer($user), null, null)
        ) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage(FrontendStrings::getCommonStrings()['time_slot_unavailable']);
            $result->setData(
                [
                    'timeSlotUnavailable' => true,
                    'status'              => $appointment->getStatus()->getValue()
                ]
            );

            return $result;
        }

        $oldAppointmentArray = $appointment->toArray();

        $capacity = 0;

        /** @var CustomerBooking $booking */
        foreach ($appointment->getBookings()->getItems() as $booking) {
            $booking->setStatus(new BookingStatus($requestedStatus));

            $capacity += $booking->getPersons()->getValue();
        }

        /** @var Service $service */
        $service = $bookableAS->getAppointmentService(
            $appointment->getServiceId()->getValue(),
            $appointment->getProviderId()->getValue()
        );

        if (
            $requestedStatus === BookingStatus::APPROVED &&
            (
                (
                    $service->getMaxCapacity()->getValue() === 1 &&
                    $capacity > 1
                ) || (
                    $service->getMaxCapacity()->getValue() > 1 &&
                    $capacity > $service->getMaxCapacity()->getValue()
                )
            )
        ) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Appointment status not updated');
            $result->setData(
                [
                    Entities::APPOINTMENT       => $appointment->toArray(),
                    'bookingsWithChangedStatus' => [],
                    'status'                    => $appointment->getStatus()->getValue(),
                    'oldStatus'                 => $appointment->getStatus()->getValue(),
                    'message'                   => BackendStrings::get('maximum_capacity_reached'),
                    'maximumCapacityReached'    => true,
                ]
            );

            return $result;
        }

        $appointment->setStatus(new BookingStatus($requestedStatus));

        $appointmentRepo->beginTransaction();

        do_action('amelia_before_appointment_status_updated', $appointment->toArray(), $requestedStatus);

        $appointmentAS->calculateAndSetAppointmentEnd($appointment, $service);

        $appointmentRepo->updateFieldById(
            $appointmentId,
            DateTimeService::getCustomDateTimeObjectInUtc(
                $appointment->getBookingEnd()->getValue()->format('Y-m-d H:i:s')
            )->format('Y-m-d H:i:s'),
            'bookingEnd'
        );


        $bookingRepository->updateFieldByColumn('status', $requestedStatus, 'appointmentId', $appointmentId);
        $appointmentRepo->updateFieldById($appointmentId, $requestedStatus, 'status');

        $appointmentRepo->commit();

        do_action('amelia_after_appointment_status_updated', $appointment->toArray(), $requestedStatus);

        /** @var CustomerBooking $booking */
        foreach ($appointment->getBookings()->getItems() as $booking) {
            if (
                $booking->getStatus()->getValue() === BookingStatus::APPROVED &&
                ($appointment->getStatus()->getValue() === BookingStatus::PENDING || $appointment->getStatus()->getValue() === BookingStatus::APPROVED)
            ) {
                $booking->setChangedStatus(new BooleanValueObject(true));
            }
        }

        $appointmentArray          = $appointment->toArray();
        $bookingsWithChangedStatus = $bookingAS->getBookingsWithChangedStatus($appointmentArray, $oldAppointmentArray);

        // Ensure provider's zoomUserId is included for Zoom integration
        if (
            $oldStatus === BookingStatus::PENDING && $requestedStatus === BookingStatus::APPROVED &&
            $appointment->getProvider() && !$appointment->getProvider()->getZoomUserId()
        ) {
            $provider = $providerRepo->getById($appointment->getProvider()->getId()->getValue());
            if ($provider && $provider->getZoomUserId()) {
                if (!isset($appointmentArray['provider'])) {
                    $appointmentArray['provider'] = [];
                }
                $appointmentArray['provider']['zoomUserId'] = $provider->getZoomUserId()->getValue();
            }
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully updated appointment status');
        $result->setData(
            [
                Entities::APPOINTMENT       => $appointmentArray,
                'bookingsWithChangedStatus' => $bookingsWithChangedStatus,
                'status'                    => $requestedStatus,
                'oldStatus'                 => $oldStatus,
                'message'                   =>
                    BackendStrings::get('appointment_status_changed') . strtolower(BackendStrings::get($requestedStatus))
            ]
        );

        return $result;
    }
}
