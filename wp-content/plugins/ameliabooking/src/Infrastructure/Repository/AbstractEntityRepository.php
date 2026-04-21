<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Repository;

use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;

/**
 * Class AbstractEntityRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository
 */
class AbstractEntityRepository extends AbstractRepository
{
    /**
     * @param int    $entityId
     * @param string $entityType
     *
     * @return bool
     * @throws QueryExecutionException
     */
    public function deleteByEntityIdAndEntityType($entityId, $entityType)
    {
        $params = [
            ':entityId'   => $entityId,
            ':entityType' => $entityType,
        ];

        try {
            $statement = $this->connection->prepare(
                "DELETE FROM {$this->table} WHERE entityId = :entityId AND entityType = :entityType"
            );

            $statement->execute($params);
            return true;
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to delete entities in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
