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
use AmeliaBooking\Domain\Factory\Schedule\BlockTimeFactory;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Schedule\DayOffRepository;
use AmeliaVendor\Psr\Container\ContainerExceptionInterface;
use Exception;

class ManageCalendarBlockTimeCommandHandler extends CommandHandler
{
    /**
     * @var array
     */
    public $mandatoryFields = [
        'name',
        'startDateTime',
        'endDateTime',
    ];

    /**
     * @param ManageCalendarBlockTimeCommand $command
     * @return CommandResult
     * @throws AccessDeniedException
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function handle(ManageCalendarBlockTimeCommand $command): CommandResult
    {
        if (!$command->getPermissionService()->currentUserCanWrite(Entities::EMPLOYEES)) {
            throw new AccessDeniedException('You are not allowed to delete block time');
        }

        $this->checkMandatoryFields($command);

        $result = new CommandResult();
        $fields = $command->getFields();

        /** @var DayOffRepository $dayOffRepository */
        $dayOffRepository = $this->container->get('domain.schedule.dayOff.repository');

        $blockTimeId = !empty($fields['id']) ? $fields['id'] : null;
        $isUpdate = $blockTimeId !== null;
        $employeeIds = !empty($fields['employeeIds']) ? $fields['employeeIds'] : [null];

        $dayOffRepository->beginTransaction();

        try {
            foreach ($employeeIds as $employeeId) {
                $blockTime = $this->createBlockTime($fields, $blockTimeId, $employeeId);

                if ($isUpdate) {
                    $dayOffRepository->update($blockTime, $blockTimeId);
                } else {
                    $dayOffRepository->add($blockTime, $employeeId);
                }
            }

            $dayOffRepository->commit();
        } catch (Exception $e) {
            $dayOffRepository->rollback();
            throw $e;
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully blocked.');
        $result->setData([]);

        return $result;
    }

    /**
     * @param array $fields
     * @param $blockTimeId
     * @param $employeeId
     * @return BlockTime
     * @throws InvalidArgumentException
     */
    private function createBlockTime(array $fields, $blockTimeId, $employeeId): BlockTime
    {
        return BlockTimeFactory::create([
            'id'        => $blockTimeId,
            'name'      => $fields['name'],
            'userId'    => $employeeId,
            'startDate' => DateTimeService::getCustomDateTimeObjectInUtc($fields['startDateTime'])->format('Y-m-d H:i:s'),
            'endDate'   => DateTimeService::getCustomDateTimeObjectInUtc($fields['endDateTime'])->format('Y-m-d H:i:s'),
        ]);
    }
}
