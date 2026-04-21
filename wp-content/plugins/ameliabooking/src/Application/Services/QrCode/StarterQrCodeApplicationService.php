<?php

namespace AmeliaBooking\Application\Services\QrCode;

/**
 * Class StarterQrCodeApplicationService
 *
 * @package AmeliaBooking\Application\Services\QrCode
 */
class StarterQrCodeApplicationService extends AbstractQrCodeApplicationService
{
    /**
     * @param array  $eventData
     * @param array  $booking
     * @param string $ticketCode
     *
     * @return array
     */

    public function createQrCodeEventTickets($eventData, $booking, $ticketCode = ''): array
    {
        return [];
    }

    /**
     * @param $event
     * @param $booking
     * @return array
     */
    public function createQrCodeEventData($event, $booking): array
    {
        return [];
    }
}
