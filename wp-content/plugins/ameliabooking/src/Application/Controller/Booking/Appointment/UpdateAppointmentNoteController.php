<?php

namespace AmeliaBooking\Application\Controller\Booking\Appointment;

use AmeliaBooking\Application\Commands\Booking\Appointment\UpdateAppointmentNoteCommand;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Controller\Controller;
use AmeliaBooking\Domain\Events\DomainEventBus;
use RuntimeException;
use Slim\Http\Request;

/**
 * Class UpdateAppointmentNoteController
 *
 * @package AmeliaBooking\Application\Controller\Booking\Appointment
 */
class UpdateAppointmentNoteController extends Controller
{
    /**
     * Fields allowed from the front-end for this endpoint
     *
     * @var array
     */
    public $allowedFields = [
        'id',
        'internalNotes',
    ];

    /**
     * @param Request $request
     * @param         $args
     *
     * @return UpdateAppointmentNoteCommand
     * @throws RuntimeException
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command = new UpdateAppointmentNoteCommand($args);

        $requestBody = $request->getParsedBody();
        $this->setCommandFields($command, $requestBody);
        $command->setToken($request);

        $params = (array)$request->getQueryParams();

        if (isset($params['source'])) {
            $command->setPage($params['source']);
        }

        return $command;
    }

    /**
     * @param DomainEventBus $eventBus
     * @param CommandResult  $result
     *
     * @return void
     */
    protected function emitSuccessEvent(DomainEventBus $eventBus, CommandResult $result)
    {
        if ($result->getResult() === CommandResult::RESULT_SUCCESS) {
            $eventBus->emit('AppointmentEdited', $result);
        }
    }
}
