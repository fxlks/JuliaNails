<?php

namespace AmeliaBooking\Application\Commands\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Booking\BookingFallbackService;
use AmeliaBooking\Application\Services\User\CustomerApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\BookingCancellationException;
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
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\CustomerBookingRepository;
use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;
use Slim\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;
use UnexpectedValueException;

/**
 * Class RejectBookingRemotelyCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Appointment
 */
class RejectBookingRemotelyCommandHandler extends CommandHandler
{
    /**
     * @var array
     */
    public $mandatoryFields = [
        'token',
    ];

    /**
     * @param RejectBookingRemotelyCommand $command
     *
     * @return CommandResult
     * @throws UnexpectedValueException
     * @throws ContainerException
     * @throws \InvalidArgumentException
     * @throws ContainerValueNotFoundException
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     * @throws AccessDeniedException
     * @throws NotFoundException
     */
    public function handle(RejectBookingRemotelyCommand $command)
    {
        $this->checkMandatoryFields($command);

        $result = new CommandResult();

        $type = $command->getField('type') ?: Entities::APPOINTMENT;

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
            if (!empty($notificationSettings['rejectErrorUrl'])) {
                $result->setUrl($notificationSettings['rejectErrorUrl']);

                return $result;
            }

            return $result->setHtml(BookingFallbackService::getFallbackHtml('failed'));
        }

        $token = $bookingRepository->getToken((int)$command->getArg('id'));

        if (empty($token['token'])) {
            throw new AccessDeniedException('You are not allowed to update booking status');
        }

        $booking->setToken(new Token($token['token']));

        if (!$customerAS->isCustomerBooking($booking, $user, $command->getField('token'))) {
            throw new AccessDeniedException('You are not allowed to update booking status');
        }

        /** @var ReservationServiceInterface $reservationService */
        $reservationService = $this->container->get('application.reservation.service')->get($type);

        $status = BookingStatus::REJECTED;

        do_action('amelia_before_booking_rejected_link', $booking ? $booking->toArray() : null);

        try {
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
        } catch (BookingCancellationException $e) {
            $result->setResult(CommandResult::RESULT_ERROR);
        }

        $notificationSettings = $settingsService->getCategorySettings('notifications');

        if ($notificationSettings['rejectSuccessUrl'] && $result->getResult() === CommandResult::RESULT_SUCCESS) {
            $result->setUrl($notificationSettings['rejectSuccessUrl']);

            do_action('amelia_after_booking_rejected_link', $booking ? $booking->toArray() : null);

            return $result;
        }

        if ($notificationSettings['rejectErrorUrl'] && $result->getResult() === CommandResult::RESULT_ERROR) {
            $result->setUrl($notificationSettings['rejectErrorUrl']);

            return $result;
        }
        // No redirect URL defined - show fallback page
        return $result->setHtml(BookingFallbackService::getFallbackHtml('rejected'));
    }
}
