<?php

namespace AmeliaBooking\Application\Commands\QrCode;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\User\UserApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Appointment\CustomerBooking;
use AmeliaBooking\Domain\Entity\Booking\Event\Event;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\CustomerBookingRepository;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Infrastructure\Repository\Booking\Event\EventRepository;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class ScanQrCodeCommandHandler
 *
 * @package AmeliaBooking\Application\Command\QrCode
 */
class ScanQrCodeCommandHandler extends CommandHandler
{
    /**
     * @var array
     */
    public $mandatoryFields = [
        'ticketManualCode',
        'scannedAt',
    ];

    /**
     * @param ScanQrCodeCommand $command
     *
     * @return CommandResult
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws ContainerException
     * @throws ContainerValueNotFoundException
     */
    public function handle(ScanQrCodeCommand $command)
    {
        $result = new CommandResult();

        /** @var UserApplicationService $userAS */
        $userAS = $this->container->get('application.user.service');

        if (!$command->getPermissionService()->currentUserCanWrite(Entities::BOOKINGS)) {
            $user = $this->container->get('logged.in.user');

            if (!$user || $user->getId() === null) {
                $user = $userAS->getAuthenticatedUser($command->getToken(), false, 'providerCabinet');

                if ($user === null) {
                    $result->setResult(CommandResult::RESULT_ERROR);
                    $result->setMessage('Could not retrieve user');
                    $result->setData(
                        [
                            'reauthorize' => true
                        ]
                    );

                    return $result;
                }
            }
        }

        $this->checkMandatoryFields($command);

        /** @var CustomerBookingRepository $bookingRepository */
        $bookingRepository = $this->container->get('domain.booking.customerBooking.repository');
        $ticketManualCode = $command->getField('ticketManualCode');

        $ticketData = $bookingRepository->getQrTicketNumber($ticketManualCode);
        if (!$ticketData) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Ticket not found');
            $result->setData([
                'messageType' => 'error',
                'message'     => 'ticket_not_found',
            ]);

            return $result;
        }

        $qrCodes = json_decode($ticketData['qrCodes'], true);
        $bookingId = $ticketData['id'];

        $scannedAt = $command->getField('scannedAt');

        /** @var CustomerBooking $booking */
        $booking = $bookingRepository->getById($bookingId);

        if (!$booking) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Booking not found');
            $result->setData([
                'messageType' => 'error',
                'message'     => 'booking_not_found',
            ]);
            return $result;
        }

        $bookingStatus = $booking->getStatus()->getValue();
        if ($bookingStatus === 'rejected' || $bookingStatus === 'canceled') {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Booking is canceled');
            $result->setData([
                'messageType' => 'error',
                'message'     => 'booking_canceled',
            ]);

            return $result;
        }

        /** @var EventRepository $eventRepository */
        $eventRepository = $this->container->get('domain.booking.event.repository');
        $eventId = $eventRepository->getByBookingId($bookingId)->getId()->getValue();
        $event = $eventRepository->getById($eventId);

        if (!$event || $event->getStatus()->getValue() === 'rejected') {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Event is canceled');
            $result->setData([
                'messageType' => 'error',
                'message'     => 'event_canceled',
            ]);

            return $result;
        }

        // Check if the scanned date is within the event's periods
        if (!$this->isDateWithinEventPeriods($event, $scannedAt)) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Ticket cannot be scanned for this date');
            $result->setData([
                'messageType' => 'error',
                'message'     => 'ticket_not_valid_for_date',
            ]);

            return $result;
        }

        $updated = false;

        $type = 'ticket';
        foreach ($qrCodes as $qrCodeItem) {
            if ($qrCodeItem['ticketManualCode'] == $ticketManualCode) {
                $type = $qrCodeItem['type'];
                break;
            }
        }

        if ($type === 'ticket') {
            foreach ($qrCodes as &$qrCode) {
                if (
                    $qrCode['type'] === 'ticket' &&
                    isset($qrCode['ticketManualCode']) &&
                    hash_equals($qrCode['ticketManualCode'], $ticketManualCode)
                ) {
                    if (isset($qrCode['dates'][$scannedAt]) && $qrCode['dates'][$scannedAt] === true) {
                        $result->setResult(CommandResult::RESULT_ERROR);
                        $result->setMessage('Ticket has already been scanned');
                        $result->setData([
                            'messageType' => 'error',
                            'message'     => 'ticket_already_scanned',
                        ]);

                        return $result;
                    }

                    $qrCode['dates'][$scannedAt] = true;
                    $updated = true;
                    break;
                }
            }
        }

        $ticketsControl = 0;

        if ($type === 'booking') {
            foreach ($qrCodes as &$qrCode) {
                if (
                    $qrCode['type'] === 'booking' &&
                    isset($qrCode['ticketManualCode']) &&
                    hash_equals($qrCode['ticketManualCode'], $ticketManualCode)
                ) {
                    if (isset($qrCode['dates'][$scannedAt]) && $qrCode['dates'][$scannedAt] === true) {
                        $result->setResult(CommandResult::RESULT_ERROR);
                        $result->setMessage('Group ticket has already been scanned');
                        $result->setData([
                            'messageType' => 'error',
                            'message'     => 'group_ticket_already_scanned',
                        ]);

                        return $result;
                    }

                    $qrCode['dates'][$scannedAt] = true;
                    $updated = true;
                    break;
                }
            }

            // Check if any ticket is already scanned for this date
            foreach ($qrCodes as $qrCodeItem) {
                if (isset($qrCodeItem['dates'][$scannedAt]) && $qrCodeItem['dates'][$scannedAt] === true) {
                    $ticketsControl++;
                }
            }

            if ($ticketsControl === count($qrCodes)) {
                $result->setResult(CommandResult::RESULT_ERROR);
                $result->setMessage('All tickets have already been scanned');
                $result->setData([
                    'messageType' => 'error',
                    'message'     => 'all_already_scanned',
                ]);

                return $result;
            }

            $ticketsControl = 0;
            // Mark all tickets as scanned for this date
            foreach ($qrCodes as &$qr) {
                if (!isset($qr['dates'][$scannedAt]) || $qr['dates'][$scannedAt] === false) {
                    $ticketsControl++;
                    $qr['dates'][$scannedAt] = true;
                }
            }
        }

        if (!$updated) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Ticket not found');
            $result->setData([
                'messageType' => 'error',
                'message'     => 'ticket_not_found',
            ]);

            return $result;
        }

        $encoded = json_encode($qrCodes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $bookingRepository->updateFieldById($bookingId, $encoded, 'qrCodes');

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Ticket is valid');
        $result->setData([
            'bookingId'        => $bookingId,
            'ticketManualCode' => $ticketManualCode,
            'type'             => $type,
            'scannedAt'        => $scannedAt,
            'qrCodes'          => $qrCodes,
            'ticketControl'    => $type === 'ticket' ? 1 : $ticketsControl,
            'eventName'        => $event ? $event->getName()->getValue() : '',
            'messageType'      => 'success',
            'message'          => 'ticket_is_valid',
        ]);

        return $result;
    }

    private function isDateWithinEventPeriods(Event $event, string $scannedAt): bool
    {
        if (!$event->getPeriods()) {
            return false;
        }

        $scannedDateTime = \DateTime::createFromFormat('Y-m-d', $scannedAt);
        if (!$scannedDateTime) {
            return false;
        }

        foreach ($event->getPeriods()->getItems() as $period) {
            $periodStart = (clone $period->getPeriodStart()->getValue())->setTime(0, 0, 0);
            $periodEnd = (clone $period->getPeriodEnd()->getValue())->setTime(23, 59, 59);

            if ($scannedDateTime >= $periodStart && $scannedDateTime <= $periodEnd) {
                return true;
            }
        }

        return false;
    }
}
