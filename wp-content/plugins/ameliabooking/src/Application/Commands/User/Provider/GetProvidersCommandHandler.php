<?php

namespace AmeliaBooking\Application\Commands\User\Provider;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\User\ProviderApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\User\ProviderRepository;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class GetProvidersCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\User\Provider
 */
class GetProvidersCommandHandler extends CommandHandler
{
    /**
     * @param GetProvidersCommand $command
     *
     * @return CommandResult
     * @throws ContainerValueNotFoundException
     * @throws QueryExecutionException
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     */
    public function handle(GetProvidersCommand $command)
    {
        if (!$command->getPermissionService()->currentUserCanRead(Entities::EMPLOYEES)) {
            throw new AccessDeniedException('You are not allowed to read employees.');
        }

        $result = new CommandResult();

        /** @var ProviderRepository $providerRepository */
        $providerRepository = $this->container->get('domain.users.providers.repository');

        /** @var ProviderApplicationService $providerService */
        $providerService = $this->container->get('application.user.provider.service');
        /** @var SettingsService $settingsService */
        $settingsService = $this->container->get('domain.settings.service');

        $companyDaysOff = $settingsService->getCategorySettings('daysOff');

        $params = $command->getField('params');

        /** @var AbstractUser $currentUser */
        $currentUser = $this->container->get('logged.in.user');

        if (
            !$command->getPermissionService()->currentUserCanReadOthers(Entities::EMPLOYEES) &&
            $currentUser->getType() === Entities::PROVIDER
        ) {
            $params['providers'][] = $currentUser->getId()->getValue();
        }

        $itemsPerPage = !empty($params['limit']) ? $params['limit'] : 10;

        /** @var Collection $providers */
        $providers = $providerRepository->getFiltered($params, $itemsPerPage);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved users.');
        $providers = $providers->toArray();

        $companyDayOff = $providerService->checkIfTodayIsCompanyDayOff($companyDaysOff);
        $providers     = $providerService->manageProvidersActivity($providers, $companyDayOff);

        $providers = apply_filters('amelia_get_providers_filter', $providers);

        do_action('amelia_get_providers', $providers);

        $result->setData(
            [
                Entities::USERS => $providers,
                'countFiltered' => (int)$providerRepository->getCount($command->getField('params')),
                'countTotal'    => (int)$providerRepository->getCount([]),
            ]
        );

        return $result;
    }
}
