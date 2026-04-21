<?php

namespace AmeliaBooking\Infrastructure\Repository\Schedule;

use AmeliaBooking\Domain\Entity\Schedule\Period;
use AmeliaBooking\Domain\Factory\Schedule\PeriodFactory;
use AmeliaBooking\Infrastructure\Licence;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;

/**
 * Class PeriodRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\Schedule
 */
class PeriodRepository extends AbstractRepository
{
    public const FACTORY = PeriodFactory::class;

    /**
     * @param Period $entity
     * @param int    $weekDayId
     *
     * @return int
     * @throws QueryExecutionException
     */
    public function add($entity, $weekDayId)
    {
        $data = $entity->toArray();

        $params = [
            ':weekDayId'  => $weekDayId,
            ':startTime'  => $data['startTime'],
            ':endTime'    => $data['endTime'],
        ];

        $additionalData = Licence\DataModifier::getPeriodRepositoryData($data);

        $params = array_merge($params, $additionalData['values']);

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO
                {$this->table}
                (
                {$additionalData['columns']}
                `weekDayId`,                
                `startTime`,
                `endTime`
                ) VALUES (
                {$additionalData['placeholders']}
                :weekDayId,
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
     * @param Period $entity
     * @param int    $id
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
            ':endTime'    => $data['endTime'],
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
                WHERE
                id = :id"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to save data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
