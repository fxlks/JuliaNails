<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Commands\Bookable\Service;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\Service\Service;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\Provider;
use AmeliaBooking\Domain\ValueObjects\String\Status;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\ServiceRepository;
use AmeliaBooking\Infrastructure\Repository\User\ProviderRepository;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class GetServicesCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Bookable\Service
 */
class GetServicesCommandHandler extends CommandHandler
{
    /**
     * @param GetServicesCommand $command
     *
     * @return CommandResult
     * @throws ContainerValueNotFoundException
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     * @throws AccessDeniedException
     * @throws ContainerException
     */
    public function handle(GetServicesCommand $command)
    {
        if (!$command->getPermissionService()->currentUserCanRead(Entities::SERVICES)) {
            throw new AccessDeniedException('You are not allowed to read services.');
        }

        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        /** @var ServiceRepository $serviceRepository */
        $serviceRepository = $this->container->get('domain.bookable.service.repository');

        /** @var ProviderRepository $providerRepository */
        $providerRepository = $this->container->get('domain.users.providers.repository');

        $params = $command->getField('params');

        $itemsPerPage = !empty($params['limit']) ? $params['limit'] : 10;

        /** @var Collection $services */
        $services = $serviceRepository->getFiltered(
            array_merge(
                $params,
                [
                    'sort' => !empty($command->getField('params')['sort'])
                        ? $command->getField('params')['sort']
                        : 'idAsc',
                ]
            ),
            $itemsPerPage
        );

        /** @var Service $service */
        foreach ($services->getItems() as $service) {
            if ($service->getSettings() && json_decode($service->getSettings()->getValue(), true) === null) {
                $service->setSettings(null);
            }
        }

        /** @var Collection $allProviders */
        $allProviders = $providerRepository->getByFieldValue('type', Entities::PROVIDER);

        $providersServices = $providerRepository->getProvidersServices($services->keys());

        $servicesArray = $services->toArray();

        // Get providers for each service
        foreach ($servicesArray as &$serviceData) {
            /** @var Collection $providers */
            $providers = new Collection();

            foreach ($providersServices as $providerId => $providerServices) {
                if (!empty($providerServices[$serviceData['id']])) {
                    /** @var Provider $provider */
                    $provider = $allProviders->getItem($providerId);

                    if ($provider->getStatus()->getValue() === Status::VISIBLE) {
                        $providers->addItem($provider, $providerId);
                    }
                }
            }

            $serviceData['employees'] = array_map(function ($provider) {
                return [
                    'id' => $provider['id'],
                    'firstName' => $provider['firstName'],
                    'lastName' => $provider['lastName'],
                    'picture' => $provider['pictureThumbPath'],
                ];
            }, $providers->toArray());
        }

        $servicesArray = apply_filters('amelia_get_services_filter', $servicesArray);

        do_action('amelia_get_services', $servicesArray);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved services.');
        $result->setData(
            [
                Entities::SERVICES => $servicesArray,
                'countFiltered'             => (int)$serviceRepository->getCount($command->getField('params')),
                'countTotalByCategory'   => (int)$serviceRepository->getCount([
                    'categoryId' => !empty($command->getField('params')['categoryId'])
                        ? $command->getField('params')['categoryId']
                        : null,
                ]),
                'countTotal'                => (int)$serviceRepository->getCount([]),
            ]
        );

        return $result;
    }
}
