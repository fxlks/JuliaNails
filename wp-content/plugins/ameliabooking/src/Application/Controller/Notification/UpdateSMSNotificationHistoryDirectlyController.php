<?php

namespace AmeliaBooking\Application\Controller\Notification;

use AmeliaBooking\Application\Commands\Notification\UpdateSMSNotificationHistoryDirectlyCommand;
use AmeliaBooking\Application\Controller\Controller;
use Slim\Http\Request;

/**
 * Class UpdateSMSNotificationHistoryDirectlyController
 *
 * @package AmeliaBooking\Application\Controller\Notification
 */
class UpdateSMSNotificationHistoryDirectlyController extends Controller
{
    /**
     * @var array
     */
    protected $allowedFields = [
        'status',
        'price',
        'dateTime',
        'logId'
    ];

    /**
     * @param Request $request
     * @param         $args
     *
     * @return UpdateSMSNotificationHistoryDirectlyCommand
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command     = new UpdateSMSNotificationHistoryDirectlyCommand($args);
        $requestBody = $request->getParsedBody();
        $this->setCommandFields($command, $requestBody);

        return $command;
    }
}
