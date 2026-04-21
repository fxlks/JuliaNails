<?php

namespace AmeliaBooking\Application\Commands\Settings;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use Exception;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class UpdateSettingsCategoriesCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Settings
 */
class UpdateSettingsCategoriesCommandHandler extends CommandHandler
{
    /**
     * @param UpdateSettingsCategoriesCommand $command
     *
     * @return CommandResult
     * @throws AccessDeniedException
     * @throws ContainerValueNotFoundException
     * @throws ContainerException
     * @throws Exception
     */
    public function handle(UpdateSettingsCategoriesCommand $command)
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

        foreach ($command->getField('categories') as $category => $data) {
            $categorySettings = $settingsService->getCategorySettings($category);

            if ($categorySettings) {
                $categorySettings = array_replace_recursive($categorySettings, $data);

                $settingsService->setCategorySettings($category, $categorySettings);
            }
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully updated settings.');

        return $result;
    }
}
