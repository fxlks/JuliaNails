<?php

namespace AmeliaBooking\Application\Commands\Settings\FeaturesIntegrations;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Services\Settings\SettingsService;

class ToggleFeatureIntegrationCommandHandler extends CommandHandler
{
    public $mandatoryFields = [
        'code'
    ];

    public function handle(ToggleFeatureIntegrationCommand $command)
    {
        $result = new CommandResult();

        if (!$command->getPermissionService()->currentUserCanWrite(Entities::SETTINGS)) {
            throw new AccessDeniedException('You are not allowed to write settings.');
        }

        $this->checkMandatoryFields($command);

        $code = $command->getField('code');

        /** @var SettingsService $settingsService */
        $settingsService = $this->getContainer()->get('domain.settings.service');

        $payments = $settingsService->getCategorySettings('payments');

        $featuresIntegrations = $settingsService->getCategorySettings('featuresIntegrations');

        if (!isset($featuresIntegrations[$code])) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Feature or integration does not exist.');

            return $result;
        }

        $featuresIntegrations[$code]['enabled'] = !$featuresIntegrations[$code]['enabled'];
        $settingsService->setCategorySettings('featuresIntegrations', $featuresIntegrations);

        if (
            isset($payments[$code]['enabled'], $featuresIntegrations[$code]) &&
            !$featuresIntegrations[$code]['enabled'] &&
            $payments[$code]['enabled']
        ) {
            $defaultPaymentMethod = 'onSite';

            $payments[$code]['enabled'] = false;

            foreach (['stripe', 'payPal', 'wc', 'mollie', 'razorpay', 'square', 'barion'] as $method) {
                if ($featuresIntegrations[$method]['enabled'] && $payments[$method]['enabled']) {
                    $defaultPaymentMethod = $method;

                    break;
                }
            }

            if ($defaultPaymentMethod === 'onSite') {
                $payments['onSite'] = true;
            }

            $payments['defaultPaymentMethod'] = $defaultPaymentMethod;

            $settingsService->setCategorySettings('payments', $payments);
        }

        return $result;
    }
}
