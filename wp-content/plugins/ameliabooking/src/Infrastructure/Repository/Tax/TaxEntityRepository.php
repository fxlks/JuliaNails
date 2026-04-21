<?php

namespace AmeliaBooking\Infrastructure\Repository\Tax;

use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\AbstractBookable;
use AmeliaBooking\Domain\Entity\Bookable\AbstractExtra;
use AmeliaBooking\Domain\Entity\Tax\Tax;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Connection;
use AmeliaBooking\Infrastructure\Repository\AbstractEntityRepository;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Tax\TaxesToEntitiesTable;

/**
 * Class TaxEntityRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\Tax
 */
class TaxEntityRepository extends AbstractEntityRepository
{
    /** @var string */
    protected $taxesToEntitiesTable;

    /**
     * @param Connection $connection
     * @param string     $table
     * @throws InvalidArgumentException
     */
    public function __construct(
        Connection $connection,
        $table
    ) {
        parent::__construct($connection, $table);

        $this->taxesToEntitiesTable = TaxesToEntitiesTable::getTableName();
    }

    /**
     * @param Tax                            $tax
     * @param AbstractBookable|AbstractExtra $entity
     *
     * @return int
     * @throws QueryExecutionException
     */
    public function add($tax, $entity)
    {
        $params = [
            ':taxId'      => $tax->getId()->getValue(),
            ':entityId'   => $entity->getId()->getValue(),
            ':entityType' => $entity->getType()->getValue(),
        ];

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO {$this->table} 
                (
                `taxId`,
                `entityType`,
                `entityId`
                )
                VALUES (
                :taxId,
                :entityType,
                :entityId
                )"
            );

            $statement->execute($params);

            return $this->connection->lastInsertId();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string     $entityType
     * @param Collection $entities
     *
     * @return bool
     * @throws QueryExecutionException
     */
    public function deleteAllForEntities($entityType, $entities)
    {
        $queryEntities = [];

        /** @var AbstractBookable|AbstractExtra $item */
        foreach ($entities->getItems() as $item) {
            $queryEntities[] = $item->getId()->getValue();
        }

        $where = 'WHERE entityType = :entityType' .
            ($queryEntities ? ' AND entityId IN (' . implode(', ', $queryEntities) . ')' : '');

        $params = [
            ':entityType' => $entityType,
        ];

        try {
            $statement = $this->connection->prepare(
                "DELETE FROM {$this->table} {$where}"
            );

            $statement->execute($params);

            return true;
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to delete data from ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
