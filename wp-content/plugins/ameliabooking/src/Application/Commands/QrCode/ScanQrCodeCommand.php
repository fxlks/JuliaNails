<?php

namespace AmeliaBooking\Application\Commands\QrCode;

use AmeliaBooking\Application\Commands\Command;

/**
 * Class ScanQrCodeCommand
 *
 * @package AmeliaBooking\Application\Commands\QrCode
 */
class ScanQrCodeCommand extends Command
{
    /**
     * ScanQrCodeCommand constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        parent::__construct($args);
    }
}
