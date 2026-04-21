<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Controller\Calendar;

use AmeliaBooking\Application\Commands\Calendar\GetBlockTimeCommand;
use AmeliaBooking\Application\Controller\Controller;
use Slim\Http\Request;

/**
 * Class GetBlockTimeController
 *
 * @package AmeliaBooking\Application\Controller\Calendar
 */
class GetBlockTimeController extends Controller
{
    /**
     * Instantiates the Get Block Time command to hand it over to the Command Handler
     *
     * @param Request $request
     * @param         $args
     *
     * @return GetBlockTimeCommand
     * @throws \RuntimeException
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command     = new GetBlockTimeCommand($args);
        $requestBody = $request->getParsedBody();
        $this->setCommandFields($command, $requestBody);

        return $command;
    }
}
