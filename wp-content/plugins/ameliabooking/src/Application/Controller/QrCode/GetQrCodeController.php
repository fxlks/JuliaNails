<?php

namespace AmeliaBooking\Application\Controller\QrCode;

use AmeliaBooking\Application\Commands\QrCode\GetQrCodeCommand;
use AmeliaBooking\Application\Controller\Controller;
use RuntimeException;
use Slim\Http\Request;

/**
 * Class GetQrCodeController
 *
 * @package AmeliaBooking\Application\Controller\QrCode
 */
class GetQrCodeController extends Controller
{
    /**
     * Instantiates the Get Qr Code command to hand it over to the Command Handler
     * @param Request $request
     * @param         $args
     * @return GetQrCodeCommand
     * @throws RuntimeException
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command = new GetQrCodeCommand($args);

        $params = (array)$request->getQueryParams();
        $command->setField('params', $params);

        $requestBody = $request->getParsedBody();
        $this->setCommandFields($command, $requestBody);

        $command->setToken($request);

        return $command;
    }
}
