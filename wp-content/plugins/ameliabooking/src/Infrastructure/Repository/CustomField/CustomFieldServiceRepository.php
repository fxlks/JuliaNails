<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Repository\CustomField;

use AmeliaBooking\Domain\Entity\CustomField\CustomFieldOption;
use AmeliaBooking\Domain\Factory\CustomField\CustomFieldOptionFactory;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;

/**
 * Class CustomFieldOptionRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\CustomField
 */
class CustomFieldServiceRepository extends AbstractRepository
{
    public const FACTORY = CustomFieldOptionFactory::class;

    /**
     * @param $customFieldId
     * @param $serviceId
     *
     * @return int
     * @throws QueryExecutionException
     */
    public function add($customFieldId, $serviceId)
    {
        $params = [
            ':customFieldId' => $customFieldId,
            ':serviceId'     => $serviceId
        ];

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO
                {$this->table}
                (
                `customFieldId`, `serviceId`
                ) VALUES (
                :customFieldId, :serviceId
                )"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $this->connection->lastInsertId();
    }

    /**
     * @param int               $id
     * @param CustomFieldOption $entity
     *
     * @return bool
     * @throws QueryExecutionException
     */
    public function update($id, $entity)
    {
        $data = $entity->toArray();

        $params = [
            ':customFieldId' => $data['customFieldId'],
            ':label'         => $data['label'],
            ':position'      => $data['position'],
            ':id'            => $id,
        ];

        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table}
                SET
                `customFieldId` = :customFieldId,
                `label`         = :label,
                `position`      = :position
                WHERE
                id = :id"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to save data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return true;
    }

    /**
     * @param int $customFieldId
     *
     * @return array
     * @throws QueryExecutionException
     */
    public function getByCustomFieldId($customFieldId)
    {
        try {
            $statement = $this->connection->query(
                "SELECT
                    cfs.id,
                    cfs.customFieldId,
                    cfs.serviceId
                FROM {$this->table} cfs
                WHERE cfs.customFieldId = {$customFieldId}"
            );

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $rows;
    }

    /**
     * @param int $customFieldId
     * @param     $serviceId
     *
     * @return bool
     * @throws QueryExecutionException
     */
    public function deleteByCustomFieldIdAndServiceId($customFieldId, $serviceId)
    {
        try {
            $statement = $this->connection->prepare(
                "DELETE FROM {$this->table} WHERE customFieldId = :customFieldId AND serviceId = :serviceId"
            );

            $statement->bindParam(':customFieldId', $customFieldId);
            $statement->bindParam(':serviceId', $serviceId);

            $statement->execute();
            return true;
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to delete data from ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
