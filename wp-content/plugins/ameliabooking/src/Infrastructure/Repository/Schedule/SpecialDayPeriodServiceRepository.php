<?php

namespace AmeliaBooking\Infrastructure\Repository\Schedule;

use AmeliaBooking\Domain\Entity\Schedule\SpecialDayPeriodService;
use AmeliaBooking\Domain\Factory\Schedule\SpecialDayPeriodServiceFactory;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;

/**
 * Class SpecialDayPeriodServiceRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\Schedule
 */
class SpecialDayPeriodServiceRepository extends AbstractRepository
{
    public const FACTORY = SpecialDayPeriodServiceFactory::class;

    /**
     * @param SpecialDayPeriodService $entity
     * @param int                     $periodId
     *
     * @return int
     * @throws QueryExecutionException
     */
    public function add($entity, $periodId)
    {
        $data = $entity->toArray();

        $params = [
            ':periodId'  => $periodId,
            ':serviceId' => $data['serviceId'],
        ];

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO {$this->table}
                (`periodId`, `serviceId`)
                VALUES (:periodId, :serviceId)"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $this->connection->lastInsertId();
    }

    /**
     * @param SpecialDayPeriodService $entity
     * @param int                     $id
     *
     * @return void
     * @throws QueryExecutionException
     */
    public function update($entity, $id)
    {
        $data = $entity->toArray();

        $params = [
            ':id'        => $id,
            ':serviceId' => $data['serviceId'],
        ];

        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table}
                SET `serviceId` = :serviceId 
                WHERE id = :id"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to save data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
