<?php

namespace AmeliaBooking\Infrastructure\Services\QrCode;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use Interop\Container\Exception\ContainerException;

/**
 * Class AbstractQrCodeInfrastructureService
 *
 * @package AmeliaBooking\Infrastructure\Services\QrCode
 */
abstract class AbstractQrCodeInfrastructureService
{
    /** @var Container $container */
    protected $container;

    /**
     * AbstractQrCodeInfrastructureService constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param array $qrData
     *
     * @return array
     *
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws ContainerException
     */
    abstract public function generateQrCode($qrData);
}
