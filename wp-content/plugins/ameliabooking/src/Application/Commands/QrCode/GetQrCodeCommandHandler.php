<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Commands\QrCode;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Booking\EventApplicationService;
use AmeliaBooking\Application\Services\QrCode\QrCodeApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\AuthorizationException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Appointment\CustomerBooking;
use AmeliaBooking\Domain\Entity\Booking\Event\Event;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Event\EventRepository;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class GetQrCodeCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\QrCode
 */
class GetQrCodeCommandHandler extends CommandHandler
{
    /**
     * @param GetQrCodeCommand $command
     *
     * @return CommandResult
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws ContainerException
     * @throws ContainerValueNotFoundException
     * @throws NotFoundException|AccessDeniedException
     */

    public function handle(GetQrCodeCommand $command)
    {
        $result = new CommandResult();

        try {
            /** @var AbstractUser $user */
            $user = $command->getUserApplicationService()->authorization(
                $command->getToken(),
                Entities::CUSTOMER
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

        /** @var EventApplicationService $eventApplicationService */
        $eventApplicationService = $this->container->get('application.booking.event.service');

        /** @var EventRepository  $eventRepository */
        $eventRepository = $this->container->get('domain.booking.event.repository');

        $params = $command->getField('params');
        $eventId = (int)($params['eventId'] ?? null);
        $bookingId = (int)($params['bookingId'] ?? null);
        $ticketManualCode = (string)($params['ticketManualCode'] ?? '');

        $eventsBookings = $eventRepository->getBookingsByCriteria([
            'customerId'            => $user->getId()->getValue(),
            'customerBookingId'     => $bookingId,
            'fetchBookingsTickets'  => true,
            'fetchBookingsPayments' => true,
        ]);

        /** @var CustomerBooking  $booking */
        $booking = $eventsBookings->getItem($eventId)->getItem($bookingId);

        if (!$booking || $booking->getCustomerId()->getValue() !== $user->getId()->getValue()) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Access denied');
            return $result;
        }

        $hasPaid = false;
        $hasOnSite = false;

        foreach ($booking->getPayments()->getItems() as $payment) {
            if (!$hasPaid && $payment->getStatus()->getValue() === 'paid') {
                $hasPaid = true;
            }
            if (!$hasOnSite && $payment->getGateway()->getName()->getValue() === 'onSite') {
                $hasOnSite = true;
            }
            if ($hasPaid && $hasOnSite) {
                break;
            }
        }

        if (!$hasPaid && !$hasOnSite) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('You have to pay full amount in order to generate E-Tickets');

            return $result;
        }

        /** @var Event $event */
        $event = $eventApplicationService->getEventById(
            $eventId,
            [
                'fetchEventsPeriods' => true,
                'fetchEventsTickets' => true,
            ]
        );

        $qrFile = null;

        if (empty($booking->getQrCodes()->getValue())) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('No E-Ticktes found for this booking');

            return $result;
        } else {
            /** @var QrCodeApplicationService $qrApplicationService */
            $qrApplicationService = $this->container->get('application.qrcode.service');
            $qrFile = $qrApplicationService->createQrCodeEventTickets(
                $event->toArray(),
                $booking->toArray(),
                $ticketManualCode
            );

            if (empty($qrFile)) {
                $result->setResult(CommandResult::RESULT_ERROR);
                $result->setMessage('No E-Ticktes found for this booking');

                return $result;
            }

            $qrFile['content'] = base64_encode($qrFile['content']);
            $qrFile['mime'] = $qrFile['type'];
            $qrFile['encoding'] = 'base64';
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setAttachment(true);
        $result->setFile($qrFile);
        $result->setMessage('Qr code generated successfully');

        return $result;
    }
}
