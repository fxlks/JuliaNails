<?php

namespace AmeliaBooking\Application\Services\Invoice;

use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use Interop\Container\Exception\ContainerException;

/**
 * Class StarterInvoiceApplicationService
 *
 * @package AmeliaBooking\Application\Services\Invoice
 */
class StarterInvoiceApplicationService extends AbstractInvoiceApplicationService
{
    /**
     * @param int $paymentId
     * @param int|null $customerId
     * @param string $format
     *
     * @return array
     *
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws ContainerException
     * @throws AccessDeniedException
     */
    public function generateInvoice($paymentId, int $customerId = null, $format = null)
    {
        return [];
    }
}
