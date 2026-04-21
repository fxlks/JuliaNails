<?php

namespace AmeliaBooking\Application\Commands\Settings;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Location\AbstractCurrentLocation;
use AmeliaBooking\Application\Services\Notification\AbstractWhatsAppNotificationService;
use AmeliaBooking\Application\Services\Stash\StashApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\ForbiddenFileUploadException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Services\Api\BasicApiService;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Services\Apple\AbstractAppleCalendarService;
use AmeliaBooking\Infrastructure\Services\LessonSpace\AbstractLessonSpaceService;
use AmeliaBooking\Infrastructure\WP\Integrations\WooCommerce\StarterWooCommerceService;
use Exception;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class UpdateSettingsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Settings
 */
class UpdateSettingsCommandHandler extends CommandHandler
{
    /**
     * @param UpdateSettingsCommand $command
     *
     * @return CommandResult
     * @throws AccessDeniedException
     * @throws ContainerValueNotFoundException
     * @throws ContainerException
     * @throws Exception
     */
    public function handle(UpdateSettingsCommand $command)
    {
        $result = new CommandResult();

        if (!$this->getContainer()->getPermissionsService()->currentUserCanWrite(Entities::SETTINGS)) {
            /** @var AbstractUser $loggedInUser */
            $loggedInUser = $this->container->get('logged.in.user');

            if (
                !$loggedInUser || !(
                    $loggedInUser->getType() === AbstractUser::USER_ROLE_ADMIN ||
                    $loggedInUser->getType() === AbstractUser::USER_ROLE_MANAGER
                )
            ) {
                throw new AccessDeniedException('You are not allowed to write settings.');
            }
        }

        /** @var SettingsService $settingsService */
        $settingsService = $this->getContainer()->get('domain.settings.service');

        /** @var AbstractCurrentLocation $locationService */
        $locationService = $this->getContainer()->get('application.currentLocation.service');

        $settingsFields = $command->getFields();

        if (
            StarterWooCommerceService::isEnabled() &&
            $command->getField('payments') &&
            $command->getField('payments')['wc']['enabled']
        ) {
            $settingsFields['payments']['wc']['productId'] = StarterWooCommerceService::getIdForExistingOrNewProduct(
                $settingsService->getCategorySettings('payments')['wc']['productId']
            );
        }


        if ($command->getField('general') && $command->getField('general')['customFieldsUploadsPath']) {
            $uploadPath = $command->getField('general')['customFieldsUploadsPath'];
            if ($uploadPath[0] !== '/') {
                throw new ForbiddenFileUploadException('Attachment upload path must be an absolute path, starting with a slash (/).');
            }

            !is_dir($uploadPath) && !mkdir($uploadPath, 0755, true);

            if (!is_writable($uploadPath) || !is_dir($uploadPath)) {
                throw new ForbiddenFileUploadException('Attachment upload path is not writable or does not exist.');
            }

            if (!file_exists("$uploadPath/index.html")) {
                file_put_contents("$uploadPath/index.html", '');
            }
        }

        if ($command->getField('sendAllCF') !== null) {
            $notificationsSettings = $settingsService->getCategorySettings('notifications');

            $settingsFields['notifications'] = $notificationsSettings;

            $settingsFields['notifications']['sendAllCF'] = $command->getField('sendAllCF');

            unset($settingsFields['sendAllCF']);
        }

        if ($command->getField('googleCalendar') !== null) {
            $googleSettings = $command->getField('googleCalendar');

            $settingsFields['googleCalendar'] = array_merge(
                $settingsService->getCategorySettings('googleCalendar'),
                $googleSettings
            );
        }

        if ($command->getField('outlookCalendar') !== null) {
            $outlookSettings = $command->getField('outlookCalendar');

            unset($outlookSettings['token']);

            $settingsFields['outlookCalendar'] = array_merge(
                $settingsService->getCategorySettings('outlookCalendar'),
                $outlookSettings
            );
        }

        if ($command->getField('providerBadges') !== null) {
            $rolesSettings = $settingsService->getCategorySettings('roles');

            $settingsFields['roles'] = $rolesSettings;

            $settingsFields['roles']['providerBadges'] = $command->getField('providerBadges');

            unset($settingsFields['providerBadges']);
        }

        if (
            !$settingsService->getCategorySettings('activation')['stash'] &&
            !empty($settingsFields['activation']['stash'])
        ) {
            /** @var StashApplicationService $stashApplicationService */
            $stashApplicationService = $this->container->get('application.stash.service');

            $stashApplicationService->setStash();
        }

        if (
            isset($settingsFields['daysOff']) &&
            $settingsService->getCategorySettings('activation')['stash'] &&
            $settingsService->getCategorySettings('daysOff') !== $settingsFields['daysOff'] &&
            $command->getField('daysOff') !== null
        ) {
            /** @var StashApplicationService $stashApplicationService */
            $stashApplicationService = $this->container->get('application.stash.service');

            $stashApplicationService->setStash($settingsFields['daysOff']);
        }

        $settingsFields['activation'] = array_merge(
            $settingsService->getCategorySettings('activation'),
            isset($settingsFields['activation']['deleteTables']) ? [
                'deleteTables' => $settingsFields['activation']['deleteTables'] ? true : false
            ] : [],
            isset($settingsFields['activation']['envatoTokenEmail']) ? [
                'envatoTokenEmail' => $settingsFields['activation']['envatoTokenEmail']
            ] : [],
            isset($settingsFields['activation']['active']) ? [
                'active' => $settingsFields['activation']['active']
            ] : [],
            isset($settingsFields['activation']['stash']) ? [
                'stash' => $settingsFields['activation']['stash']
            ] : [],
            isset($settingsFields['activation']['showAmeliaPromoCustomizePopup']) ? [
                'showAmeliaPromoCustomizePopup' => $settingsFields['activation']['showAmeliaPromoCustomizePopup']
            ] : [],
            isset($settingsFields['activation']['showAmeliaSurvey']) ? [
                'showAmeliaSurvey' => $settingsFields['activation']['showAmeliaSurvey']
            ] : [],
            isset($settingsFields['activation']['customUrl']) ? [
                'customUrl' => $settingsFields['activation']['customUrl']
            ] : [],
            isset($settingsFields['activation']['v3AsyncLoading']) ? [
                'v3AsyncLoading' => $settingsFields['activation']['v3AsyncLoading']
            ] : [],
            isset($settingsFields['activation']['v3RelativePath']) ? [
                'v3RelativePath' => $settingsFields['activation']['v3RelativePath']
            ] : [],
            isset($settingsFields['activation']['enableThriveItems']) ? [
                'enableThriveItems' => $settingsFields['activation']['enableThriveItems']
            ] : [],
            isset($settingsFields['activation']['responseErrorAsConflict']) ? [
                'responseErrorAsConflict' => $settingsFields['activation']['responseErrorAsConflict']
            ] : [],
            isset($settingsFields['activation']['disableUrlParams']) ? [
                'disableUrlParams' => $settingsFields['activation']['disableUrlParams']
            ] : [],
            isset($settingsFields['activation']['hideUnavailableFeatures']) ? [
                'hideUnavailableFeatures' => $settingsFields['activation']['hideUnavailableFeatures']
            ] : [],
            isset($settingsFields['activation']['hideTipsAndSuggestions']) ? [
                'hideTipsAndSuggestions' => $settingsFields['activation']['hideTipsAndSuggestions']
            ] : [],
            isset($settingsFields['activation']['licence']) ? [
                'licence' => $settingsFields['activation']['licence']
            ] : [],
            isset($settingsFields['activation']['premiumBannerVisibility']) ? [
                'premiumBannerVisibility' => $settingsFields['activation']['premiumBannerVisibility']
            ] : [],
            isset($settingsFields['activation']['dismissibleBannerVisibility']) ? [
                'dismissibleBannerVisibility' => $settingsFields['activation']['dismissibleBannerVisibility']
            ] : []
        );

        if ($command->getField('usedLanguages') !== null) {
            $generalSettings = $settingsService->getCategorySettings('general');

            $settingsFields['general'] = $generalSettings;

            $settingsFields['general']['usedLanguages'] = $command->getField('usedLanguages');

            unset($settingsFields['usedLanguages']);
        }

        if ($command->getField('lessonSpace') !== null && $settingsFields['lessonSpace']['apiKey']) {
            if (!$settingsService->getCategorySettings('lessonSpace')['companyId']) {
                /** @var AbstractLessonSpaceService $lessonSpaceService */
                $lessonSpaceService = $this->container->get('infrastructure.lesson.space.service');

                $companyDetails = $lessonSpaceService->getCompanyId($settingsFields['lessonSpace']['apiKey']);

                $settingsFields['lessonSpace']['companyId'] = $companyDetails['id'];
            } else {
                $settingsFields['lessonSpace']['companyId'] = $settingsService->getCategorySettings('lessonSpace')['companyId'];
            }
        }

        if ($command->getField('payments') && !empty($command->getField('payments')['square'])) {
            $settingsFields['payments']['square']['accessToken'] = $settingsService->getCategorySettings('payments')['square']['accessToken'];
        }

        if (isset($settingsFields['apiKeys']) && isset($settingsFields['apiKeys']['apiKeys'])) {
            /** @var BasicApiService $apiService */
            $apiService = $this->getContainer()->get('domain.api.service');
            foreach ($settingsFields['apiKeys']['apiKeys'] as $index => $apiKey) {
                if (!empty($apiKey['isNew'])) {
                    $settingsFields['apiKeys']['apiKeys'][$index]['key'] = $apiService->createHash($settingsFields['apiKeys']['apiKeys'][$index]['key']);
                }
                unset($settingsFields['apiKeys']['apiKeys'][$index]['isNew']);
            }
        }

        if (isset($settingsFields['pageColumnSettings'])) {
            $currentPageColumnSettings = $settingsService->getCategorySettings('pageColumnSettings');

            foreach ($settingsFields['pageColumnSettings'] as $page => $columns) {
                $currentPageColumnSettings[$page] = $columns;
            }

            $settingsFields['pageColumnSettings'] = $currentPageColumnSettings;
        }

        if (!empty($command->getField('appleCalendar')['clientID']) && !empty($command->getField('appleCalendar')['clientSecret'])) {
            /** @var AbstractAppleCalendarService $appleCalendarService */
            $appleCalendarService = $this->container->get('infrastructure.apple.calendar.service');

            $appleId       = $command->getField('appleCalendar')['clientID'];
            $applePassword = $command->getField('appleCalendar')['clientSecret'];

            $credentials = $appleCalendarService->handleAppleCredentials($appleId, $applePassword);

            if (!$credentials) {
                $result->setDataInResponse(true);
                $result->setResult(CommandResult::RESULT_ERROR);
                $result->setMessage('Make sure you are using the correct iCloud email address and app-specific password.');

                return $result;
            }
        }

        if (
            $command->getField('appleCalendar') &&
            empty($command->getField('appleCalendar')['clientID']) &&
            empty($command->getField('appleCalendar')['clientSecret'])
        ) {
            $providerRepository = $this->container->get('domain.users.providers.repository');
            /** @var Collection $providers */
            $providers = $providerRepository->getAll();
            foreach ($providers->getItems() as $provider) {
                $providerRepository->updateFieldById($provider->getId()->getValue(), null, 'employeeAppleCalendar');
                $providerRepository->updateFieldById($provider->getId()->getValue(), null, 'appleCalendarId');
            }
        }

        if (!empty($command->getField('customizedData'))) {
            $passedCustomizedData = $command->getField('customizedData');
            $customizedData       = $settingsService->getCategorySettings('customizedData');

            foreach ($passedCustomizedData as $key => $value) {
                $customizedData[$key] = $value;
            }

            $settingsFields['customizedData'] = $customizedData;
        }

        $settingsFields = apply_filters('amelia_before_settings_updated_filter', $settingsFields);

        do_action('amelia_before_settings_updated', $settingsFields);

        $settingsService->setAllSettings($settingsFields);

        $settings = $settingsService->getAllSettingsCategorized();
        $settings['general']['phoneDefaultCountryCode'] = $settings['general']['phoneDefaultCountryCode'] === 'auto' ?
            $locationService->getCurrentLocationCountryIso($settings['general']['ipLocateApiKey']) : $settings['general']['phoneDefaultCountryCode'];

        do_action('amelia_after_settings_updated', $settingsFields);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully updated settings.');
        $result->setData(
            [
                'settings' => $settings
            ]
        );

        return $result;
    }
}
