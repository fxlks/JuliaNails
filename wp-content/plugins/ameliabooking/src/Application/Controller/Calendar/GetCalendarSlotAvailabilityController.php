<?php

namespace AmeliaBooking\Application\Controller\Calendar;

use AmeliaBooking\Application\Commands\Calendar\GetCalendarSlotAvailabilityCommand;
use AmeliaBooking\Application\Commands\Command;
use AmeliaBooking\Application\Controller\Controller;
use Slim\Http\Request;

class GetCalendarSlotAvailabilityController extends Controller
{
    public $allowedFields = [
        'bookingStart',
        'timeZone',
        'appointmentId'
    ];

    /**
     * @param Request $request
     * @param array   $args
     *
     * @return Command
     */
    protected function instantiateCommand(Request $request, $args): Command
    {
        $command = new GetCalendarSlotAvailabilityCommand($args);

        $queryParams = $request->getQueryParams();

        $this->setCommandFields($command, $queryParams);

        return $command;
    }
}
