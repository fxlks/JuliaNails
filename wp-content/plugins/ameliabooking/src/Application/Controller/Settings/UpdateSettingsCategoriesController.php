<?php

namespace AmeliaBooking\Application\Controller\Settings;

use AmeliaBooking\Application\Commands\Settings\UpdateSettingsCategoriesCommand;
use AmeliaBooking\Application\Controller\Controller;
use RuntimeException;
use Slim\Http\Request;

/**
 * Class UpdateSettingsCategoriesController
 *
 * @package AmeliaBooking\Application\Controller\Settings
 */
class UpdateSettingsCategoriesController extends Controller
{
    /**
     * Fields for settings category that can be received from front-end
     *
     * @var array
     */
    protected $allowedFields = [
        'categories',
    ];

    /**
     * @param Request $request
     * @param         $args
     *
     * @return UpdateSettingsCategoriesCommand
     * @throws RuntimeException
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command = new UpdateSettingsCategoriesCommand($args);

        $requestBody = $request->getParsedBody();

        $this->setCommandFields($command, $requestBody);

        return $command;
    }
}
