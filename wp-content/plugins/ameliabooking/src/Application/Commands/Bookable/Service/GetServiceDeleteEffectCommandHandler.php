<?php

namespace AmeliaBooking\Application\Commands\Bookable\Service;

use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Bookable\BookableApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\PackageRepository;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class GetServiceDeleteEffectCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Bookable\Service
 */
class GetServiceDeleteEffectCommandHandler extends CommandHandler
{
    /**
     * @param GetServiceDeleteEffectCommand $command
     *
     * @return CommandResult
     * @throws ContainerValueNotFoundException
     * @throws AccessDeniedException
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function handle(GetServiceDeleteEffectCommand $command)
    {
        if (!$command->getPermissionService()->currentUserCanRead(Entities::SERVICES)) {
            throw new AccessDeniedException('You are not allowed to read services');
        }

        $result = new CommandResult();

        /** @var PackageRepository $packageRepository */
        $packageRepository = $this->container->get('domain.bookable.package.repository');

        /** @var BookableApplicationService $bookableAS */
        $bookableAS = $this->getContainer()->get('application.bookable.service');

        $appointmentsCount = $bookableAS->getAppointmentsCountForServices([$command->getArg('id')]);

        $messageKey = '';
        $messageData = null;

        if ($appointmentsCount['futureAppointments'] > 0) {
            $messageKey = 'red_delete_service_effect_future';
            $messageData = ['count' => $appointmentsCount['futureAppointments']];
        } elseif ($appointmentsCount['packageAppointments']) {
            $messageKey = 'red_delete_service_effect_package';
        } elseif ($appointmentsCount['pastAppointments'] > 0) {
            $messageKey = 'red_delete_service_effect_past';
            $messageData = ['count' => $appointmentsCount['pastAppointments']];
        }

        /** @var Collection $packages */
        $packages = $packageRepository->getByCriteria(['services' => [$command->getArg('id')]]);

        if ($packages->length()) {
            $messageKey = 'red_service_failed_to_be_deleted';
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved message.');
        $result->setData(
            [
                'valid'       => !$appointmentsCount['futureAppointments'] && !$packages->length(),
                'messageKey'  => $messageKey,
                'messageData' => $messageData,
            ]
        );

        return $result;
    }
}
