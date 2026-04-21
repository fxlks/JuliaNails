<?php

namespace AmeliaBooking\Application\Services\QrCode;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use Interop\Container\Exception\ContainerException;

/**
 * Class AbstractQrCodeApplicationService
 *
 * @package AmeliaBooking\Application\Services\QrCode
 */
abstract class AbstractQrCodeApplicationService
{
    /** @var Container $container */
    protected $container;

    /**
     * AbstractQrCodeApplicationService constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param array  $eventData
     * @param array  $booking
     * @param string $ticketCode
     *
     * @return array
     *
     * @throws QueryExecutionException
     * @throws ContainerException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    abstract public function createQrCodeEventTickets($eventData, $booking, $ticketCode = ''): array;

    /**
     * @param $event
     * @param $booking
     * @return array
     */
    abstract public function createQrCodeEventData($event, $booking): array;
}
