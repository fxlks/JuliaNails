<?php

namespace AmeliaBooking\Application\Controller\Settings\FeaturesIntegrations;

use AmeliaBooking\Application\Commands\Settings\FeaturesIntegrations\ToggleFeatureIntegrationCommand;
use AmeliaBooking\Application\Controller\Controller;
use Slim\Http\Request;

class ToggleFeatureIntegrationController extends Controller
{
    /**
     * Fields for user that can be received from front-end
     *
     * @var array
     */
    protected $allowedFields = [
        'code',
    ];

    protected function instantiateCommand(Request $request, $args)
    {
        $command     = new ToggleFeatureIntegrationCommand($args);
        $requestBody = $request->getParsedBody();
        $this->setCommandFields($command, $requestBody);

        return $command;
    }
}
