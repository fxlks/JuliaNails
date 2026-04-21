<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Controller\Booking\Event;

use AmeliaBooking\Application\Commands\Bookable\Service\UpdateServiceStatusCommand;
use AmeliaBooking\Application\Commands\Booking\Event\UpdateEventVisibilityCommand;
use AmeliaBooking\Application\Controller\Controller;
use Slim\Http\Request;

/**
 * Class UpdateEventVisibilityController
 *
 * @package AmeliaBooking\Application\Controller\Booking\Event
 */
class UpdateEventVisibilityController extends Controller
{
    /**
     * Fields for event that can be received from front-end
     *
     * @var array
     */
    protected $allowedFields = [
        'status',
        'applyGlobally'
    ];

    /**
     * Instantiates the Update Service Status command to hand it over to the Command Handler
     *
     * @param Request $request
     * @param         $args
     *
     * @return UpdateEventVisibilityCommand
     * @throws \RuntimeException
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command = new UpdateEventVisibilityCommand($args);
        $requestBody = $request->getParsedBody();
        $this->setCommandFields($command, $requestBody);

        return $command;
    }
}
