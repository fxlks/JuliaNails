<?php

namespace AmeliaBooking\Application\Commands\Square;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Validation\ValidationService;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Infrastructure\Services\Payment\SquareService;

/**
 * Class FetchAccessTokenSquareCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Square
 */
class FetchAccessTokenSquareCommandHandler extends CommandHandler
{
    /**
     * @param FetchAccessTokenSquareCommand $command
     *
     * @return CommandResult
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException
     * @throws AccessDeniedException
     */
    public function handle(FetchAccessTokenSquareCommand $command)
    {
        if (!$this->getContainer()->getPermissionsService()->currentUserCanWrite(Entities::SETTINGS)) {
            throw new AccessDeniedException('You are not allowed to write settings.');
        }

        $params = $command->getFields();

        $apiPath = '/square/authorization/token&decrypted_access_token=' . $params['decrypted_access_token']
            . '&expires_at=' . $params['expires_at'] . '&decrypted_refresh_token=' . $params['decrypted_refresh_token']
            . '&merchant_id=' . $params['merchant_id']
            . '&access_token=' . $params['access_token'] . '&refresh_token=' . $params['refresh_token'];

        if (!ValidationService::verifySignature($apiPath, 'middleware', !empty($params['signature']) ? $params['signature'] : null)) {
            throw new AccessDeniedException('Signature mismatch.');
        }

        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        /** @var SquareService $squareService */
        $squareService = $this->container->get('infrastructure.payment.square.service');

        /** @var \AmeliaBooking\Domain\Services\Settings\SettingsService $settingsService */
        $settingsService = $this->container->get('domain.settings.service');


        $accessToken = $command->getFields();

        $squareSettings = $settingsService->getCategorySettings('payments')['square'];

        if (empty($accessToken) || empty($accessToken['access_token'])) {
            $squareSettings['enabled']     = false;
            $squareSettings['accessToken'] = null;
            $settingsService->setSetting('payments', 'square', $squareSettings);

            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('There has been an error retrieving the access token');
            $result->setUrl(AMELIA_SITE_URL . '/wp-admin/admin.php?page=wpamelia-settings/payment?square=1&square_error=1');

            return $result;
        }

        set_transient(
            'amelia_square_access_token',
            [
                'access_token' => $accessToken['decrypted_access_token'],
                'refresh_token' => $accessToken['decrypted_refresh_token']
            ],
            604800
        );

        $squareService->registerDomainForApplePay();

        unset($accessToken['decrypted_access_token']);
        unset($accessToken['decrypted_refresh_token']);

        $squareSettings['accessToken'] = $accessToken;

        $squareSettings['enabled'] = true;

        $settingsService->setSetting('payments', 'square', $squareSettings);

        $locations = $squareService->getLocations();

        if ($locations && sizeof($locations)) {
            $squareSettings = $settingsService->getCategorySettings('payments')['square'];
            $squareSettings['locationId'] = $locations[0]->getId();
            $squareSettings['countryCode'] = $locations[0]->getCountry();
            $settingsService->setSetting('payments', 'square', $squareSettings);
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully fetched access token');
        $result->setData(
            [
                'locations' => $locations
            ]
        );

        $result->setUrl(AMELIA_SITE_URL . '/wp-admin/admin.php?page=wpamelia-settings#/payment?square=1');

        return $result;
    }
}
