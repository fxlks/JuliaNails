<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Repository\Cache;

use AmeliaBooking\Domain\Entity\Cache\Cache;
use AmeliaBooking\Domain\Factory\Cache\CacheFactory;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Connection;

/**
 * Class CacheRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\Cache
 */
class CacheRepository extends AbstractRepository
{
    /**
     * @param Connection $connection
     * @param string     $table
     */
    public function __construct(
        Connection $connection,
        $table
    ) {
        parent::__construct($connection, $table);
    }

    public const FACTORY = CacheFactory::class;

    /**
     * @param Cache $entity
     *
     * @return int
     * @throws QueryExecutionException
     */
    public function add($entity)
    {
        $data = $entity->toArray();

        $params = [
            ':name' => $data['name'],
            ':data' => $data['data'],
        ];

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO
                {$this->table} 
                (
                `name`,
                `data`
                ) VALUES (
                :name,
                :data
                )"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $this->connection->lastInsertId();
    }

    /**
     * @param int   $id
     * @param Cache $entity
     *
     * @return bool
     * @throws QueryExecutionException
     */
    public function update($id, $entity)
    {
        $data = $entity->toArray();

        $params = [
            ':paymentId' => $data['paymentId'],
            ':data'      => $data['data'],
            ':id'        => $id,
        ];

        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table}
                SET
                `paymentId` = :paymentId,
                `data` = :data
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
     * @param int    $id
     * @param string $name
     *
     * @return Cache|null
     * @throws QueryExecutionException
     */
    public function getByIdAndName($id, $name)
    {
        try {
            $statement = $this->connection->prepare(
                $this->selectQuery() . " WHERE id = :id AND name = :name"
            );

            $params = [
                ':id'   => $id,
                ':name' => $name
            ];

            $statement->execute($params);

            $row = $statement->fetch();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        if (!$row) {
            return null;
        }

        return call_user_func([static::FACTORY, 'create'], $row);
    }
}
