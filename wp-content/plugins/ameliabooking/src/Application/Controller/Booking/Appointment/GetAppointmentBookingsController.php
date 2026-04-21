<?php

namespace AmeliaBooking\Application\Controller\Booking\Appointment;

use AmeliaBooking\Application\Commands\Booking\Appointment\GetAppointmentBookingsCommand;
use AmeliaBooking\Application\Controller\Controller;
use RuntimeException;
use Slim\Http\Request;

/**
 * Class GetAppointmentBookingsController
 *
 * @package AmeliaBooking\Application\Controller\Booking\Appointment
 */
class GetAppointmentBookingsController extends Controller
{
    /**
     * Instantiates the Get Appointment Bookings command to hand it over to the Command Handler
     *
     * @param Request $request
     * @param         $args
     *
     * @return GetAppointmentBookingsCommand
     * @throws RuntimeException
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command = new GetAppointmentBookingsCommand($args);

        $params = (array)$request->getQueryParams();

        $this->setArrayParams($params, ['status']);

        if (empty($params['dates'][0])) {
            $params['dates'][0] = null;
        }

        if (empty($params['dates'][1])) {
            $params['dates'][1] = null;
        }

        if (!empty($params['providers'])) {
            $params['providers'] = array_map('intval', $params['providers']);
        }

        if (!empty($params['customers'])) {
            $params['customers'] = array_map('intval', $params['customers']);
        }

        if (!empty($params['services'])) {
            $params['services'] = array_map('intval', $params['services']);
        }

        $command->setField('params', $params);

        $command->setToken($request);

        return $command;
    }
}
