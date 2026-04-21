<?php

namespace AmeliaBooking\Application\Controller\User\Customer;

use AmeliaBooking\Application\Commands\User\Customer\UpdateCustomerNoteCommand;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Controller\Controller;
use AmeliaBooking\Domain\Events\DomainEventBus;
use Slim\Http\Request;

/**
 * Class UpdateCustomerNoteController
 *
 * @package AmeliaBooking\Application\Controller\User\Customer
 */
class UpdateCustomerNoteController extends Controller
{
    /**
     * Fields allowed from the front-end for this endpoint
     *
     * @var array
     */
    protected $allowedFields = [
        'note',
    ];

    /**
     * @param Request $request
     * @param         $args
     *
     * @return UpdateCustomerNoteCommand
     * @throws \RuntimeException
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command = new UpdateCustomerNoteCommand($args);

        $requestBody = $request->getParsedBody();
        $this->setCommandFields($command, $requestBody);
        $command->setField('id', $args['id']);
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
            $eventBus->emit('user.updated', $result);
        }
    }
}
