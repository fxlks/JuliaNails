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
 * Class DeleteBlockTimeCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Calendar
 */
class DeleteBlockTimeCommandHandler extends CommandHandler
{
    /**
     * @param DeleteBlockTimeCommand $command
     *
     * @return CommandResult
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws QueryExecutionException|ContainerExceptionInterface
     */
    public function handle(DeleteBlockTimeCommand $command): CommandResult
    {
        if (!$command->getPermissionService()->currentUserCanWrite(Entities::EMPLOYEES)) {
            throw new AccessDeniedException('You are not allowed to delete block time');
        }

        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        /** @var DayOffRepository $dayOffRepository */
        $dayOffRepository = $this->container->get('domain.schedule.dayOff.repository');

        /** @var BlockTime $blockTime */
        $blockTime = $dayOffRepository->getBlockTimeById($command->getArg('id'));

        $dayOffRepository->beginTransaction();

        do_action('amelia_before_block_time_deleted', $blockTime->toArray());

        if (!$dayOffRepository->delete($command->getArg('id'))) {
            $dayOffRepository->rollback();

            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Unable to delete block time.');

            return $result;
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully deleted block time.');
        $result->setData([
            'blockTime' => $blockTime->toArray()
        ]);

        $dayOffRepository->commit();

        do_action('amelia_after_block_time_deleted', $blockTime->toArray());

        return $result;
    }
}
