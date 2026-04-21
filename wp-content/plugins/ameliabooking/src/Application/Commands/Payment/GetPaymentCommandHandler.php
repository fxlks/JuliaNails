<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Commands\Payment;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Payment\PaymentApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Payment\Payment;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Services\Reservation\ReservationServiceInterface;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Payment\PaymentRepository;

/**
 * Class GetPaymentCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Payment
 */
class GetPaymentCommandHandler extends CommandHandler
{
    /**
     * @param GetPaymentCommand $command
     *
     * @return CommandResult
     * @throws QueryExecutionException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws AccessDeniedException
     */
    public function handle(GetPaymentCommand $command)
    {
        if (!$command->getPermissionService()->currentUserCanRead(Entities::FINANCE)) {
            throw new AccessDeniedException('You are not allowed to read payment.');
        }

        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        /** @var PaymentApplicationService $paymentAS */
        $paymentAS = $this->container->get('application.payment.service');

        $params = $command->getField('params');

        $paymentId = $command->getArg('id');

        $paymentsData = $paymentAS->getPaymentsData(
            [
                'ids'      => [$paymentId],
                'invoices' => !empty($params['invoices']),
            ]
        );

        if (empty($paymentsData)) {
            throw new NotFoundException('Payment not found.');
        }

        /** @var ReservationServiceInterface $reservationService */
        $reservationService = $this->container->get('application.reservation.service')->get(
            $paymentsData[$paymentId]['type']
        );

        $paymentsData[$paymentId]['summary'] = $reservationService->getPaymentSummary(
            $paymentsData[$paymentId],
            !empty($params['invoices'])
        );

        $paymentArray = reset($paymentsData);

        $paymentArray = apply_filters('amelia_get_payment_filter', $paymentArray);

        do_action('amelia_get_payment', $paymentArray);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved payment.');
        $result->setData([Entities::PAYMENT => $paymentArray]);

        return $result;
    }
}
