<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Repository\Bookable\Service;

use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\Service\Resource;
use AmeliaBooking\Domain\Factory\Bookable\Service\ResourceFactory;
use AmeliaBooking\Infrastructure\Connection;
use AmeliaBooking\Infrastructure\DB\WPDB\Statement;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\ResourcesToEntitiesTable;

/**
 * Class ResourceRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\Service
 */
class ResourceRepository extends AbstractRepository
{
    public const FACTORY = ResourceFactory::class;

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

    /**
     * @param Resource $entity
     *
     * @return int
     * @throws QueryExecutionException
     */
    public function add($entity)
    {
        $data = $entity->toArray();

        $params = [
            ':name'      => $data['name'],
            ':quantity'  => $data['quantity'],
            ':status'    => $data['status'],
            ':shared'    => $data['shared'] ? $data['shared'] : null,
            ':countAdditionalPeople' => $data['countAdditionalPeople'] ? 1 : 0
        ];

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO 
                {$this->table} 
                (
                `name`, 
                `quantity`,
                `status`, 
                `shared`,
                 `countAdditionalPeople`
                ) VALUES (
                :name,
                :quantity,
                :status,
                :shared,
                :countAdditionalPeople
                )"
            );

            $statement->execute($params);

            return $this->connection->lastInsertId();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int     $resourceId
     * @param Resource $entity
     *
     * @throws QueryExecutionException
     */
    public function update($resourceId, $entity)
    {
        $data = $entity->toArray();

        $params = [
            ':name'             => $data['name'],
            ':quantity'         => $data['quantity'],
            ':status'           => $data['status'],
            ':shared'           => $data['shared'] ? $data['shared'] : null,
            ':countAdditionalPeople' => $data['countAdditionalPeople'] ? 1 : 0,
            ':id'               => $resourceId
        ];


        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table}
                SET
                `name`              = :name,
                `quantity`          = :quantity,
                `status`            = :status,
                `shared`            = :shared,
                `countAdditionalPeople` = :countAdditionalPeople
                WHERE
                id = :id"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to save data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param $criteria
     *
     * @return Collection
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function getByCriteria($criteria)
    {
        $params = [];
        $where = [];

        // Define ordering
        $order = 'ORDER BY r.id ASC';
        if (isset($criteria['sort'])) {
            if ($criteria['sort'] === '-id') {
                $order = 'ORDER BY r.id DESC';
            } elseif ($criteria['sort'] === 'name') {
                $order = 'ORDER BY r.name ASC';
            } elseif ($criteria['sort'] === '-name') {
                $order = 'ORDER BY r.name DESC';
            } elseif ($criteria['sort'] === 'quantity') {
                $order = 'ORDER BY r.quantity ASC';
            } elseif ($criteria['sort'] === '-quantity') {
                $order = 'ORDER BY r.quantity DESC';
            }
        }

        if (!empty($criteria['search'])) {
            $params[':search1'] = $params[':search2'] = "%{$criteria['search']}%";

            $where[] = '(r.name LIKE :search1 OR r.id LIKE :search2)';
        }

        if (!empty($criteria['services'])) {
            $query = [];
            foreach ((array)$criteria['services'] as $index => $value) {
                $param   = ':service' . $index;
                $query[] = $param;

                $params[$param] = $value;
            }
            $where[] = 're.entityId IN (' . implode(', ', $query) . ') AND re.entityType="service"';
        }

        if (!empty($criteria['locations'])) {
            $query = [];
            foreach ((array)$criteria['locations'] as $index => $value) {
                $param   = ':location' . $index;
                $query[] = $param;

                $params[$param] = $value;
            }
            $where[] = 're.entityId IN (' . implode(', ', $query) . ') AND re.entityType="location"';
        }

        if (!empty($criteria['employees'])) {
            $query = [];
            foreach ((array)$criteria['employees'] as $index => $value) {
                $param   = ':employee' . $index;
                $query[] = $param;

                $params[$param] = $value;
            }
            $where[] = 're.entityId IN (' . implode(', ', $query) . ') AND re.entityType="employee"';
        }

        if (!empty($criteria['status'])) {
            $params[':status'] = $criteria['status'];

            $where[] = 'r.status = :status';
        }

        $whereSql = $where ? ' AND ' . implode(' AND ', $where) : '';

        $resourceEntitiesTable = ResourcesToEntitiesTable::getTableName();

        // Define common SELECT and FROM parts
        $selectSql = "SELECT
            r.id AS resource_id,
            r.name AS resource_name,
            r.quantity AS resource_quantity,
            r.status AS resource_status,
            r.shared AS resource_shared,
            r.countAdditionalPeople AS resource_countAdditionalPeople,
            
            re.id AS resource_entity_id,
            re.resourceId AS resource_entity_resourceId,
            re.entityId AS resource_entity_entityId,
            re.entityType AS resource_entity_entityType";

        $fromSql = "FROM {$this->table} r
            LEFT JOIN {$resourceEntitiesTable} re ON re.resourceId = r.id";

        try {
            // PAGINATION: If limit is set, use subquery to get resource IDs first
            if (!empty($criteria['limit'])) {
                $itemsPerPage = (int)$criteria['limit'];
                $page = !empty($criteria['page']) ? (int)$criteria['page'] : 1;
                $offset = ($page - 1) * $itemsPerPage;

                // 1. Get paginated resource IDs
                $idSql = "SELECT DISTINCT r.id FROM {$this->table} r 
                    LEFT JOIN {$resourceEntitiesTable} re ON re.resourceId = r.id 
                    WHERE 1 = 1{$whereSql} {$order} 
                    LIMIT {$itemsPerPage} OFFSET {$offset}";

                $idStmt = $this->connection->prepare($idSql);
                $idStmt->execute($params);
                $resourceIds = $idStmt->fetchAll(Statement::FETCH_COLUMN);

                if (empty($resourceIds)) {
                    return call_user_func([static::FACTORY, 'createCollection'], []);
                }

                // 2. Prepare ID parameters for IN clause
                $inParams = [];
                foreach ($resourceIds as $idx => $id) {
                    $inParams[":r_id{$idx}"] = $id;
                }
                $inClause = implode(',', array_keys($inParams));
                $params = array_merge($params, $inParams);

                $whereSqlWithIds = $whereSql ? $whereSql . ' AND ' : ' AND ';
                $whereSqlWithIds .= "r.id IN ($inClause)";

                // 3. Get complete resource data
                $sql = "{$selectSql} {$fromSql} WHERE 1 = 1 {$whereSqlWithIds} {$order}";
                $statement = $this->connection->prepare($sql);
                $statement->execute($params);
                $rows = $statement->fetchAll();
            } else {
                // Regular query without pagination
                $sql = "{$selectSql} {$fromSql} WHERE 1 = 1 {$whereSql} {$order}";
                $statement = $this->connection->prepare($sql);
                $statement->execute($params);
                $rows = $statement->fetchAll();
            }
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by criteria in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return call_user_func([static::FACTORY, 'createCollection'], $rows);
    }

    /**
     * @param $id
     *
     * @return Collection
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function getById($id)
    {
        $params[':id'] = $id;

        $resourceEntitiesTable = ResourcesToEntitiesTable::getTableName();

        try {
            $statement = $this->connection->prepare(
                "SELECT
                r.id AS resource_id,
                r.name AS resource_name,
                r.quantity AS resource_quantity,
                r.status AS resource_status,
                r.shared AS resource_shared,
                r.countAdditionalPeople AS resource_countAdditionalPeople,
                
                re.id AS resource_entity_id,
                re.resourceId AS resource_entity_resourceId,
                re.entityId AS resource_entity_entityId,
                re.entityType AS resource_entity_entityType
                
                FROM {$this->table} r
                LEFT JOIN {$resourceEntitiesTable} re ON re.resourceId = r.id
                WHERE r.id = :id"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return call_user_func([static::FACTORY, 'createCollection'], $rows)->getItem($id);
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws QueryExecutionException|InvalidArgumentException
     */
    public function delete($id)
    {
        $resourceToEntities = ResourcesToEntitiesTable::getTableName();

        $params = [
            ':id'  => $id,
        ];

        try {
            $statement = $this->connection->prepare(
                "DELETE FROM {$this->table} WHERE id = :id"
            );
            $statement->execute($params);
            $statement = $this->connection->prepare(
                "DELETE FROM {$resourceToEntities} WHERE resourceId = :id"
            );
            $statement->execute($params);

            return true;
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to delete data from ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getCount($criteria)
    {
        $params = [];
        $where = [];

        if (!empty($criteria['search'])) {
            $params[':search1'] = $params[':search2'] = "%{$criteria['search']}%";
            $where[] = '(r.name LIKE :search1 OR r.id LIKE :search2)';
        }

        if (!empty($criteria['services'])) {
            $query = [];
            foreach ((array)$criteria['services'] as $index => $value) {
                $param = ':service' . $index;
                $query[] = $param;
                $params[$param] = $value;
            }
            $where[] = 're.entityId IN (' . implode(', ', $query) . ') AND re.entityType="service"';
        }

        if (!empty($criteria['locations'])) {
            $query = [];
            foreach ((array)$criteria['locations'] as $index => $value) {
                $param = ':location' . $index;
                $query[] = $param;
                $params[$param] = $value;
            }
            $where[] = 're.entityId IN (' . implode(', ', $query) . ') AND re.entityType="location"';
        }

        if (!empty($criteria['employees'])) {
            $query = [];
            foreach ((array)$criteria['employees'] as $index => $value) {
                $param = ':employee' . $index;
                $query[] = $param;
                $params[$param] = $value;
            }
            $where[] = 're.entityId IN (' . implode(', ', $query) . ') AND re.entityType="employee"';
        }

        if (!empty($criteria['status'])) {
            $params[':status'] = $criteria['status'];
            $where[] = 'r.status = :status';
        }

        $where = $where ? ' AND ' . implode(' AND ', $where) : '';

        $resourceEntitiesTable = ResourcesToEntitiesTable::getTableName();

        try {
            $statement = $this->connection->prepare(
                "SELECT COUNT(DISTINCT r.id) AS count
                FROM {$this->table} r
                LEFT JOIN {$resourceEntitiesTable} re ON re.resourceId = r.id
                WHERE 1 = 1 {$where}"
            );

            $statement->execute($params);

            $row = $statement->fetch()['count'];
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $row;
    }
}
