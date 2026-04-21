<?php

namespace AmeliaBooking\Infrastructure\Services\QrCode;

use Interop\Container\Exception\ContainerException;

/**
 * Class StarterQrCodeInfrastructureService
 *
 * @package AmeliaBooking\Infrastructure\Services\QrCode
 */
class StarterQrCodeInfrastructureService extends AbstractQrCodeInfrastructureService
{
    /**
     * @param array $qrData
     *
     * @return array
     *
     * @throws ContainerException
     */
    public function generateQrCode($qrData)
    {
        return [];
    }
}
