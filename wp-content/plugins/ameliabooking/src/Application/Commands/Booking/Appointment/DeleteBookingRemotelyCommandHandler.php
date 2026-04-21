<?php

namespace AmeliaBooking\Application\Commands\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Reservation\AbstractReservationService;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\Payment\Payment;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use Slim\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class DeleteBookingRemotelyCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Appointment
 */
class DeleteBookingRemotelyCommandHandler extends CommandHandler
{
    /**
     * @param DeleteBookingRemotelyCommand $command
     *
     * @return CommandResult
     * @throws InvalidArgumentException
     * @throws ContainerValueNotFoundException
     * @throws AccessDeniedException
     * @throws QueryExecutionException
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function handle(DeleteBookingRemotelyCommand $command)
    {
        $result = new CommandResult();

        $type = $command->getField('type') ?: Entities::APPOINTMENT;

        $bookingId = $command->getArg('id');

        $token = $command->getField('token');

        if (!$token) {
            throw new AccessDeniedException('No token sent.');
        }

        /** @var AbstractReservationService $reservationService */
        $reservationService = $this->container->get('application.reservation.service')->get($type);

        $booking = $reservationService->getBooking($bookingId);

        if (!$booking->getToken() || $booking->getToken()->getValue() !== $token) {
            throw new AccessDeniedException('Invalid token sent.');
        }

        if ($booking->getPayments()->length() > 0 && $booking->getPayments()->getItem($booking->getPayments()->keys()[0])) {
            /** @var Payment $payment */
            $payment = $booking->getPayments()->getItem($booking->getPayments()->keys()[0]);
            $now = new \DateTime();
            $diffInSeconds = $now->getTimestamp() -
                DateTimeService::getCustomDateTimeObjectInUtc($payment->getCreated()->getValue()->format('Y-m-d H:i:s'))->getTimestamp();
            if ($diffInSeconds > 1800) {
                throw new AccessDeniedException('Token expired.');
            }
        }

        try {
            $reservationService->deleteBooking($bookingId);
        } catch (\Exception $e) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage($e->getMessage());

            return $result;
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully deleted booking');

        return $result;
    }
}
