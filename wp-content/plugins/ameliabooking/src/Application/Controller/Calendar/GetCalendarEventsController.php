<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Controller\Calendar;

use Slim\Http\Request;
use AmeliaBooking\Application\Commands\Calendar\GetCalendarEventsCommand;
use AmeliaBooking\Application\Commands\Command;
use AmeliaBooking\Application\Controller\Controller;

class GetCalendarEventsController extends Controller
{
    /**
     * @param Request $request
     * @param array   $args
     *
     * @return Command
     */
    protected function instantiateCommand(Request $request, $args): Command
    {
        $command = new GetCalendarEventsCommand($args);

        $queryParams = $request->getQueryParams();

        $this->setArrayParams($queryParams, ['entitiesToShow']);

        $command->setField('queryParams', $queryParams);

        return $command;
    }
}
