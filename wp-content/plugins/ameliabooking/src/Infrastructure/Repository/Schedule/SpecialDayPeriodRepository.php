<?php

namespace AmeliaBooking\Infrastructure\Repository\Schedule;

use AmeliaBooking\Domain\Entity\Schedule\SpecialDayPeriod;
use AmeliaBooking\Domain\Factory\Schedule\SpecialDayPeriodFactory;
use AmeliaBooking\Infrastructure\Licence;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;

/**
 * Class SpecialDayPeriodRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\Schedule
 */
class SpecialDayPeriodRepository extends AbstractRepository
{
    public const FACTORY = SpecialDayPeriodFactory::class;

    /**
     * @param SpecialDayPeriod $entity
     * @param int              $specialDayId
     *
     * @return int
     * @throws QueryExecutionException
     */
    public function add($entity, $specialDayId)
    {
        $data = $entity->toArray();

        $params = [
            ':specialDayId' => $specialDayId,
            ':startTime'    => $data['startTime'],
            ':endTime'      => $data['endTime']
        ];

        $additionalData = Licence\DataModifier::getPeriodRepositoryData($data);

        $params = array_merge($params, $additionalData['values']);

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO
                {$this->table}
                (
                {$additionalData['columns']}
                `specialDayId`,
                `startTime`,
                `endTime`
                ) VALUES (
                {$additionalData['placeholders']}
                :specialDayId,
                :startTime,
                :endTime
                )"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $this->connection->lastInsertId();
    }

    /**
     * @param SpecialDayPeriod $entity
     * @param int              $id
     *
     * @return void
     * @throws QueryExecutionException
     */
    public function update($entity, $id)
    {
        $data = $entity->toArray();

        $params = [
            ':id'         => $id,
            ':startTime'  => $data['startTime'],
            ':endTime'    => $data['endTime']
        ];

        $additionalData = Licence\DataModifier::getPeriodRepositoryData($data);

        $params = array_merge($params, $additionalData['values']);

        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table}
                SET
                {$additionalData['columnsPlaceholders']}
                `startTime` = :startTime,
                `endTime` = :endTime
                WHERE id = :id"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to save data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
