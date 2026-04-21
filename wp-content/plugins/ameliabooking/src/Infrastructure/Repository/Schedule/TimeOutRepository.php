<?php

namespace AmeliaBooking\Infrastructure\Repository\Schedule;

use AmeliaBooking\Domain\Entity\Schedule\TimeOut;
use AmeliaBooking\Domain\Factory\Schedule\TimeOutFactory;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;

/**
 * Class TimeOutRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\Schedule
 */
class TimeOutRepository extends AbstractRepository
{
    public const FACTORY = TimeOutFactory::class;

    /**
     * @param TimeOut $entity
     * @param int     $weekDayId
     *
     * @return int
     * @throws QueryExecutionException
     */
    public function add($entity, $weekDayId)
    {
        $data = $entity->toArray();

        $params = [
            ':weekDayId' => $weekDayId,
            ':startTime' => $data['startTime'],
            ':endTime'   => $data['endTime'],
        ];

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO {$this->table}
                (`weekDayId`, `startTime`, `endTime`)
                VALUES (:weekDayId, :startTime, :endTime)"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $this->connection->lastInsertId();
    }

    /**
     * @param TimeOut $entity
     * @param int     $id
     *
     * @return void
     * @throws QueryExecutionException
     */
    public function update($entity, $id)
    {
        $data = $entity->toArray();

        $params = [
            ':id'        => $id,
            ':startTime' => $data['startTime'],
            ':endTime'   => $data['endTime'],
        ];

        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table}
                SET `startTime` = :startTime, `endTime` = :endTime
                WHERE id = :id"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to save data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
