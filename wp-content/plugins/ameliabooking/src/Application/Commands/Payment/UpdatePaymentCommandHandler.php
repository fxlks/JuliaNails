<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Commands\Payment;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Notification\ApplicationNotificationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Payment\Payment;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Factory\Booking\Event\EventFactory;
use AmeliaBooking\Domain\Factory\Payment\PaymentFactory;
use AmeliaBooking\Domain\Services\Reservation\ReservationServiceInterface;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Domain\ValueObjects\Number\Integer\Id;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Payment\PaymentRepository;

/**
 * Class UpdatePaymentCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Payment
 */
class UpdatePaymentCommandHandler extends CommandHandler
{
    /**
     * @param UpdatePaymentCommand $command
     *
     * @return CommandResult
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     * @throws AccessDeniedException
     */
    public function handle(UpdatePaymentCommand $command)
    {
        if (!$command->getPermissionService()->currentUserCanWrite(Entities::FINANCE)) {
            throw new AccessDeniedException('You are not allowed to update payment.');
        }

        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        $paymentArray = $command->getFields();

        $paymentArray = apply_filters('amelia_before_payment_updated_filter', $paymentArray);

        do_action('amelia_before_payment_updated', $paymentArray);

        $payment = PaymentFactory::create($paymentArray);

        if (!$payment instanceof Payment) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Unable to update payment.');

            return $result;
        }

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $this->container->get('domain.payment.repository');

        /** @var SettingsService $settingsDS */
        $settingsDS = $this->container->get('domain.settings.service');

        $paymentId = (int)$command->getArg('id');
        if ($paymentRepository->update($paymentId, $payment)) {
            $payment->setId(new Id($paymentId));
            do_action('amelia_after_payment_updated', $payment->toArray());

            $paymentEntity = $payment->getEntity() ? $payment->getEntity()->getValue() : null;
            $paymentStatus = $payment->getStatus() ? $payment->getStatus()->getValue() : null;

            if (
                $settingsDS->isFeatureEnabled('eTickets') &&
                $paymentEntity === Entities::EVENT &&
                $paymentStatus === 'paid'
            ) {
                /** @var ReservationServiceInterface $reservationService */
                $reservationService = $this->container->get('application.reservation.service')->get(
                    $paymentEntity
                );
                /** @var ApplicationNotificationService $applicationNotificationService */
                $applicationNotificationService = $this->container->get('application.notification.service');

                $customerBookingId = $payment->getCustomerBookingId() ? $payment->getCustomerBookingId()->getValue() : null;
                $reservationEvent = $reservationService->getReservationByBookingId($customerBookingId);

                $bookingKey = null;

                foreach ($reservationEvent->getBookings()->toArray() as $index => $reservationBooking) {
                    if ($reservationBooking['id'] === $customerBookingId) {
                        $bookingKey = $index;
                    }
                }

                $applicationNotificationService->sendEventQrNotification(
                    EventFactory::create($reservationEvent->toArray()),
                    $bookingKey
                );
            }

            $result->setResult(CommandResult::RESULT_SUCCESS);
            $result->setMessage('Payment successfully updated.');
            $result->setData(
                [
                    Entities::PAYMENT => $payment->toArray(),
                ]
            );
        }

        return $result;
    }
}
