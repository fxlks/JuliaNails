<?php

namespace AmeliaBooking\Application\Controller\Square;

use AmeliaBooking\Application\Commands\Square\DisconnectFromSquareAccountDirectlyCommand;
use AmeliaBooking\Application\Controller\Controller;
use Slim\Http\Request;

/**
 * Class DisconnectFromSquareAccountDirectlyController
 *
 * @package AmeliaBooking\Application\Controller\Square
 */
class DisconnectFromSquareAccountDirectlyController extends Controller
{
    /**
     * Fields that can be received from front-end
     *
     * @var array
     */
    public $allowedFields = [
        'data'
    ];


    /**
     * @param Request $request
     * @param         $args
     *
     * @return DisconnectFromSquareAccountDirectlyCommand
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command     = new DisconnectFromSquareAccountDirectlyCommand($args);
        $requestBody = $request->getParsedBody();
        $this->setCommandFields($command, $requestBody);
        $command->setToken($request);

        return $command;
    }
}
