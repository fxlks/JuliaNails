<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Commands\Payment;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Commands\SortParamsTrait;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Payment\PaymentApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Services\Reservation\ReservationServiceInterface;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Payment\PaymentRepository;

/**
 * Class GetPaymentsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Payment
 */
class GetPaymentsCommandHandler extends CommandHandler
{
    use SortParamsTrait;

    /**
     * @param GetPaymentsCommand $command
     *
     * @return CommandResult
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     * @throws AccessDeniedException
     */
    public function handle(GetPaymentsCommand $command)
    {
        if (!$command->getPermissionService()->currentUserCanRead(Entities::FINANCE)) {
            throw new AccessDeniedException('You are not allowed to read payments.');
        }

        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $this->container->get('domain.payment.repository');

        /** @var PaymentApplicationService $paymentAS */
        $paymentAS = $this->container->get('application.payment.service');

        $params = $command->getField('params');

        if (!empty($params['dates'])) {
            $params['dates'][0] .= ' 00:00:00';
            $params['dates'][1] .= ' 23:59:59';
        }

        $params = $this->parseSortParams($params, ['id', 'created', 'status']);

        $paymentsData = $paymentAS->getPaymentsData(
            $params,
            $params['limit'] ?? 10
        );

        $isInvoicePage = !empty($params['invoices']) && filter_var($params['invoices'], FILTER_VALIDATE_BOOLEAN);

        $payments = [];

        foreach ($paymentsData as $paymentId => $payment) {
            /** @var ReservationServiceInterface $reservationService */
            $reservationService = $this->container->get('application.reservation.service')->get(
                $payment['type']
            );

            $paymentsData[$paymentId]['summary'] = $reservationService->getPaymentSummary(
                $payment,
                $isInvoicePage
            );

            $payments[] = $paymentsData[$paymentId];
        }

        $payments = apply_filters('amelia_get_payments_filter', $payments);

        do_action('amelia_get_payments', $payments);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved payments.');
        $result->setData(
            [
                Entities::PAYMENTS => $payments,
                'filteredCount'    => (int)$paymentRepository->getFilteredIdsCount($params, $isInvoicePage),
                'totalCount'       => (int)$paymentRepository->getFilteredIdsCount(
                    [
                        'separateRows' => !empty($params['separateRows']),
                    ],
                    $isInvoicePage
                ),
            ]
        );

        return $result;
    }
}
