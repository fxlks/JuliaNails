<?php

namespace AmeliaBooking\Application\Controller\Calendar;

use AmeliaBooking\Application\Commands\Calendar\ManageCalendarBlockTimeCommand;
use AmeliaBooking\Application\Commands\Command;
use AmeliaBooking\Application\Controller\Controller;
use Slim\Http\Request;

class ManageCalendarBlockTimeController extends Controller
{
    public $allowedFields = [
        'id',
        'name',
        'startDateTime',
        'endDateTime',
        'employeeIds'
    ];

    /**
     * @param Request $request
     * @param array   $args
     *
     * @return Command
     */
    protected function instantiateCommand(Request $request, $args): Command
    {
        $command = new ManageCalendarBlockTimeCommand($args);

        $requestBody = $request->getParsedBody();

        $this->setCommandFields($command, $requestBody);

        return $command;
    }
}
