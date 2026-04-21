<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Controller\Calendar;

use AmeliaBooking\Application\Commands\Calendar\GetCalendarSlotEntitiesCommand;
use AmeliaBooking\Application\Controller\Controller;
use Slim\Http\Request;

class GetCalendarSlotEntitiesController extends Controller
{
    public $allowedFields = [
        'date',
        'time'
    ];

    protected function instantiateCommand(Request $request, $args): GetCalendarSlotEntitiesCommand
    {
        $command = new GetCalendarSlotEntitiesCommand($args);

        $queryParams = $request->getQueryParams();

        $this->setCommandFields($command, $queryParams);

        return $command;
    }
}
