<?php

namespace AmeliaBooking\Application\Controller\WhatsNew;

use AmeliaBooking\Application\Commands\WhatsNew\GetWhatsNewCommand;
use AmeliaBooking\Application\Controller\Controller;
use RuntimeException;
use Slim\Http\Request;

/**
 * Class UpdateStashController
 *
 * @package AmeliaBooking\Application\Controller\Stash
 */
class GetWhatsNewController extends Controller
{
    protected $allowedFields = ['page', 'limit', 'category'];

    /**
     * @param Request $request
     * @param $args
     * @return GetWhatsNewCommand
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command = new GetWhatsNewCommand($args);

        $requestParams = $request->getQueryParams();

        $this->setCommandFields($command, $requestParams);

        $command->setToken($request);

        return $command;
    }
}
