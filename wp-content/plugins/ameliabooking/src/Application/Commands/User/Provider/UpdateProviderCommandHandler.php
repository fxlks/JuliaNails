<?php

namespace AmeliaBooking\Application\Commands\User\Provider;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Entity\EntityApplicationService;
use AmeliaBooking\Application\Services\User\ProviderApplicationService;
use AmeliaBooking\Application\Services\User\UserApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Entity\User\Provider;
use AmeliaBooking\Domain\Factory\User\UserFactory;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Domain\ValueObjects\String\Password;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\User\ProviderRepository;
use AmeliaBooking\Infrastructure\Services\Apple\AbstractAppleCalendarService;
use Exception;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;
use AmeliaBooking\Domain\ValueObjects\String\Name;
use AmeliaBooking\Domain\ValueObjects\String\Phone;

/**
 * Class UpdateProviderCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\User\Provider
 */
class UpdateProviderCommandHandler extends CommandHandler
{
    /**
     * @param UpdateProviderCommand $command
     *
     * @return CommandResult
     * @throws ContainerValueNotFoundException
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws ContainerException
     * @throws Exception
     */
    public function handle(UpdateProviderCommand $command)
    {
        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        /** @var ProviderRepository $providerRepository */
        $providerRepository = $this->container->get('domain.users.providers.repository');

        /** @var ProviderApplicationService $providerAS */
        $providerAS = $this->container->get('application.user.provider.service');

        /** @var SettingsService $settingsDS */
        $settingsDS = $this->container->get('domain.settings.service');

        $userId = (int)$command->getArg('id');

        /** @var AbstractUser $currentUser */
        $currentUser = $this->container->get('logged.in.user');

        /** @var UserApplicationService $userAS */
        $userAS = $this->getContainer()->get('application.user.service');

        if (
            !$command->getPermissionService()->currentUserCanWrite(Entities::EMPLOYEES) ||
            (
                !$command->getPermissionService()->currentUserCanWriteOthers(Entities::EMPLOYEES) &&
                (
                    !$currentUser->getId() ||
                    $currentUser->getId()->getValue() !== $userId
                )
            )
        ) {
            $oldUser = $userAS->getAuthenticatedUser($command->getToken(), false, 'providerCabinet');

            if (
                $oldUser === null ||
                ($command->getField('externalId') && (!$oldUser->getExternalId() || $oldUser->getExternalId()->getValue() !== $command->getField('externalId')))
            ) {
                $result->setResult(CommandResult::RESULT_ERROR);
                $result->setMessage('Could not retrieve user');
                $result->setData(
                    [
                        'reauthorize' => true
                    ]
                );

                return $result;
            }

            $oldUser = $providerAS->getProviderWithServicesAndSchedule($oldUser->getId()->getValue());
        } else {
            $oldUser = $providerAS->getProviderWithServicesAndSchedule($userId);
        }

        $command->setField('id', $userId);

        $providerData = $command->getFields();

        if (!isset($providerData['stripeConnect'])) {
            $providerData['stripeConnect'] = null;
        }

        if (!isset($providerData['zoomUserId'])) {
            $providerData['zoomUserId'] = null;
        }

        if (!isset($providerData['appleCalendarId'])) {
            $providerData['appleCalendarId'] = null;
        }

        if (!isset($providerData['employeeAppleCalendar'])) {
            $providerData['employeeAppleCalendar'] = null;
        } else {
            /** @var AbstractAppleCalendarService $appleCalendarService */
            $appleCalendarService = $this->container->get('infrastructure.apple.calendar.service');

            $appleId       = $providerData['employeeAppleCalendar']['iCloudId'];
            $applePassword = $providerData['employeeAppleCalendar']['appSpecificPassword'];

            $credentials = $appleCalendarService->handleAppleCredentials($appleId, $applePassword);

            if (!$credentials) {
                $providerData['employeeAppleCalendar'] = null;
            }
        }

        /** @var EntityApplicationService $entityService */
        $entityService = $this->container->get('application.entity.service');

        $entityService->removeMissingEntitiesForProvider($providerData);

        if (!!$oldUser->getBadgeId() && !isset($providerData['badgeId'])) {
            $providerData['badgeId'] = null;
        }

        if ($oldUser->getTimeZone() && $settingsDS->isFeatureEnabled('timezones') === false) {
            $providerData['timeZone'] = $oldUser->getTimeZone()->getValue();
        }

        $newUserData = array_merge($oldUser->toArray(), $providerData);

        $newUserData = apply_filters('amelia_before_provider_updated_filter', $newUserData, $oldUser->toArray());

        /** @var Provider $newUser */
        $newUser = UserFactory::create($newUserData);

        $oldExternalId = $oldUser->getExternalId() ? $oldUser->getExternalId()->getValue() : null;
        $newExternalId = $newUser->getExternalId() ? $newUser->getExternalId()->getValue() : null;

        if ($oldExternalId !== $newExternalId && (!$currentUser || $currentUser->getType() !== AbstractUser::USER_ROLE_ADMIN)) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Could not update user.');
            return $result;
        }

        // If the phone is not set and the old phone is set, set the phone and country phone iso to null
        if (empty($providerData['phone']) && $oldUser->getPhone() && $oldUser->getPhone()->getValue()) {
            $newUser->setPhone(new Phone(null));
            $newUser->setCountryPhoneIso(new Name(null));
        }

        $newUser->setDayOffList(
            $providerAS->getModifiedDayList(
                $newUser->getDayOffList(),
                $oldUser->getDayOffList(),
                !empty($newUserData['removedDayOffList'])
                    ? UserFactory::createDayOffList($newUserData['removedDayOffList'])
                    : new Collection()
            )
        );

        $newUser->setSpecialDayList(
            $providerAS->getModifiedDayList(
                $newUser->getSpecialDayList(),
                $oldUser->getSpecialDayList(),
                !empty($newUserData['removedSpecialDayList'])
                    ? UserFactory::createSpecialDayList($newUserData['removedSpecialDayList'])
                    : new Collection()
            )
        );

        if ($command->getUserApplicationService()->checkProviderPermissions($currentUser, $command->getToken())) {
            $rolesSettings = $settingsDS->getCategorySettings('roles');

            if (!$rolesSettings['allowConfigureServices']) {
                $newUser->setServiceList($oldUser->getServiceList());
            }

            if (!$rolesSettings['allowConfigureSchedule']) {
                $newUser->setWeekDayList($oldUser->getWeekDayList());
            }

            if (!$rolesSettings['allowConfigureDaysOff']) {
                $newUser->setDayOffList($oldUser->getDayOffList());
            }

            if (!$rolesSettings['allowConfigureSpecialDays']) {
                $newUser->setSpecialDayList($oldUser->getSpecialDayList());
            }
        }

        $providerRepository->beginTransaction();

        if (
            $providerRepository->getByEmail($newUser->getEmail()->getValue()) &&
            $oldUser->getEmail()->getValue() !== $newUser->getEmail()->getValue()
        ) {
            $result->setResult(CommandResult::RESULT_CONFLICT);
            $result->setMessage('Email already exist.');
            $result->setData('This email is already in use.');

            return $result;
        }

        if ($command->getField('password')) {
            $newPassword = new Password($command->getField('password'));

            $providerRepository->updateFieldById($command->getArg('id'), $newPassword->getValue(), 'password');

            if ($newUser->getExternalId() && $newUser->getExternalId()->getValue()) {
                add_filter('amelia_user_profile_updated', '__return_true');
                wp_set_password($command->getField('password'), $newUser->getExternalId()->getValue());
                remove_filter('amelia_user_profile_updated', '__return_true');
            }
        }

        do_action('amelia_before_provider_updated', $newUser ? $newUser->toArray() : null, $oldUser ? $oldUser->toArray() : null);

        try {
            if (!$providerAS->update($oldUser, $newUser)) {
                $providerRepository->rollback();
                return $result;
            }

            if ($command->getField('externalId') === 0) {
                /** @var UserApplicationService $userAS */
                $userAS = $this->getContainer()->get('application.user.service');

                $userAS->setWpUserIdForNewUser($userId, $newUser, Entities::PROVIDER, $command->getField('password'));
            } elseif ($newUser->getExternalId() && $newUser->getExternalId()->getValue()) {
                add_filter('amelia_user_profile_updated', '__return_true');
                wp_update_user(
                    [
                        'ID' => $newUser->getExternalId()->getValue(),
                        'first_name' => $newUser->getFirstName() ? $newUser->getFirstName()->getValue() : '',
                        'last_name'  => $newUser->getLastName() ? $newUser->getLastName()->getValue() : '',
                        'user_email' => $newUser->getEmail() ? $newUser->getEmail()->getValue() : ''
                    ]
                );

                if ($uid = get_current_user_id()) {
                    clean_user_cache($uid);
                }

                remove_filter('amelia_user_profile_updated', '__return_true');
            }
        } catch (QueryExecutionException $e) {
            $providerRepository->rollback();
            throw $e;
        }

        $result = $userAS->getAuthenticatedUserResponse(
            $newUser,
            $oldUser->getEmail()->getValue() !== $newUser->getEmail()->getValue(),
            true,
            $oldUser->getLoginType(),
            'provider'
        );

        $result->setData(
            array_merge(
                $result->getData(),
                [
                    'sendEmployeePanelAccessEmail' =>
                    $command->getField('password') && $command->getField('sendEmployeePanelAccessEmail'),
                    'password'                     => $command->getField('password')
                ]
            )
        );

        $providerRepository->commit();

        do_action('amelia_after_provider_updated', $newUser ? $newUser->toArray() : null, $oldUser ? $oldUser->toArray() : null);

        return $result;
    }
}
