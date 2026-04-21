<?php

namespace AmeliaBooking\Application\Controller\Bookable\Category;

use AmeliaBooking\Application\Commands\Bookable\Category\GetCategoryDeleteEffectCommand;
use AmeliaBooking\Application\Controller\Controller;
use Slim\Http\Request;

/**
 * Class GetCategoryDeleteEffectController
 *
 * @package AmeliaBooking\Application\Controller\Bookable\Category
 */
class GetCategoryDeleteEffectController extends Controller
{
    /**
     * Instantiates the Get Category Delete Effect command to hand it over to the Command Handler
     *
     * @param Request $request
     * @param         $args
     *
     * @return GetCategoryDeleteEffectCommand
     * @throws \RuntimeException
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command = new GetCategoryDeleteEffectCommand($args);
        $requestBody = $request->getParsedBody();
        $this->setCommandFields($command, $requestBody);

        return $command;
    }
}
