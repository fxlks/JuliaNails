<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Commands\Calendar;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\Schedule\BlockTime;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Schedule\DayOffRepository;
use AmeliaVendor\Psr\Container\ContainerExceptionInterface;

/**
 * Class GetBlockTimeCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Calendar
 */
class GetBlockTimeCommandHandler extends CommandHandler
{
    /**
     * @param GetBlockTimeCommand $command
     *
     * @return CommandResult
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException|ContainerExceptionInterface
     */
    public function handle(GetBlockTimeCommand $command): CommandResult
    {
        if (!$command->getPermissionService()->currentUserCanRead(Entities::APPOINTMENTS)) {
            throw new AccessDeniedException('You are not allowed to read block time');
        }

        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        /** @var DayOffRepository $dayOffRepository */
        $dayOffRepository = $this->container->get('domain.schedule.dayOff.repository');

        /** @var BlockTime $blockTime */
        $blockTime = $dayOffRepository->getBlockTimeById($command->getArg('id'));

        $blockTimeArray = $blockTime->toArray();

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved block time.');
        $result->setData([
            'blockTime' => $blockTimeArray
        ]);

        return $result;
    }
}
