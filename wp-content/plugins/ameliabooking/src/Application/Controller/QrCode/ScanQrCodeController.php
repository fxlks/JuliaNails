<?php

namespace AmeliaBooking\Application\Controller\QrCode;

use AmeliaBooking\Application\Commands\QrCode\ScanQrCodeCommand;
use AmeliaBooking\Application\Controller\Controller;
use RuntimeException;
use Slim\Http\Request;

/**
 * Class ScanQrCodeController
 *
 * @package AmeliaBooking\Application\Controller\QrCode
 */
class ScanQrCodeController extends Controller
{
    public $allowedFields = [
        'ticketManualCode',
        'scannedAt',
    ];

    /**
     * Instantiates the Scan Qr Code command to hand it over to the Command Handler
     * @param Request $request
     * @param         $args
     * @return ScanQrCodeCommand
     * @throws RuntimeException
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command = new ScanQrCodeCommand($args);

        $params = (array)$request->getQueryParams();
        $command->setField('params', $params);

        $requestBody = $request->getParsedBody();
        $this->setCommandFields($command, $requestBody);

        $command->setToken($request);

        return $command;
    }
}
