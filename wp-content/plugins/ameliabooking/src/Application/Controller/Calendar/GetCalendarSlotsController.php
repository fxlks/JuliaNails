<?php

namespace AmeliaBooking\Application\Controller\Calendar;

use AmeliaBooking\Application\Commands\Calendar\GetCalendarSlotsCommand;
use AmeliaBooking\Application\Commands\Command;
use AmeliaBooking\Application\Controller\Controller;
use Slim\Http\Request;

class GetCalendarSlotsController extends Controller
{
    /**
     * @param Request $request
     * @param array   $args
     *
     * @return Command
     */
    protected function instantiateCommand(Request $request, $args): Command
    {
        $command = new GetCalendarSlotsCommand($args);

        $queryParams = $request->getQueryParams();

        $this->setArrayParams($queryParams, ['entitiesToShow']);

        $command->setField('queryParams', $queryParams);

        /** @var array $requestBody */
        $requestBody = $request->getParsedBody();

        $this->setCommandFields($command, $requestBody);

        return $command;
    }
}
