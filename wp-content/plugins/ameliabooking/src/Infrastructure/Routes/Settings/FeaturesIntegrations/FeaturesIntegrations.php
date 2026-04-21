<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See COPYING.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Routes\Settings\FeaturesIntegrations;

use AmeliaBooking\Application\Controller\Settings\FeaturesIntegrations\ToggleFeatureIntegrationController;
use Slim\App;

class FeaturesIntegrations
{
    /**
     * @param App $app
     */
    public static function routes(App $app)
    {
        $app->post('/settings/features-integrations/toggle', ToggleFeatureIntegrationController::class);
    }
}
