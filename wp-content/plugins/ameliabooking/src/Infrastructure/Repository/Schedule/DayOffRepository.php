<?php

namespace AmeliaBooking\Infrastructure\Repository\Schedule;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Schedule\BlockTime;
use AmeliaBooking\Domain\Entity\Schedule\DayOff;
use AmeliaBooking\Domain\Factory\Schedule\BlockTimeFactory;
use AmeliaBooking\Domain\Factory\Schedule\DayOffFactory;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\UsersTable;

/**
 * Class DayOffRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\Schedule
 */
class DayOffRepository extends AbstractRepository
{
    public const FACTORY = DayOffFactory::class;

    /**
     * @param DayOff | BlockTime $entity
     * @param int | null $userId
     *
     * @return int
     * @throws QueryExecutionException
     */
    public function add($entity, $userId)
    {
        $data = $entity->toArray();

        $params = [
            ':userId'    => $userId,
            ':name'      => $data['name'],
            ':startDate' => $data['startDate'],
            ':endDate'   => $data['endDate'],
            ':type'      => $data['type'],
            ':repeat'    => $data['repeat'],
        ];

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO {$this->table}
                (`userId`, `name`, `startDate`, `endDate`, `repeat`, `type`)
                VALUES
                (:userId, :name, :startDate, :endDate, :repeat, :type)"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $this->connection->lastInsertId();
    }

    /**
     * @param DayOff $entity
     * @param int    $id
     *
     * @return void
     * @throws QueryExecutionException
     */
    public function update($entity, $id)
    {
        $data = $entity->toArray();

        $params = [
            ':id'        => $id,
            ':name'      => $data['name'],
            ':startDate' => $data['startDate'],
            ':endDate'   => $data['endDate'],
            ':repeat'    => $data['repeat'],
        ];

        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table}
                SET `name` = :name, `startDate` = :startDate, `endDate` = :endDate, `repeat` = :repeat
                WHERE id = :id"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add save in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $criteria
     * @return mixed
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function getFiltered($criteria)
    {
        $userTable = UsersTable::getTableName();

        try {
            $params = [];

            $where = [];

            if (!empty($criteria['dates'][0]) && !empty($criteria['dates'][1])) {
                $where[] = "((do.startDate BETWEEN :dayOffFrom1 AND :dayOffTo1)
                OR (do.endDate BETWEEN :dayOffFrom2 AND :dayOffTo2)
                OR (:dayOffFrom3 BETWEEN do.startDate AND do.endDate)
                OR (:dayOffTo3  BETWEEN do.startDate AND do.endDate))";

                $params[':dayOffFrom1'] = $params[':dayOffFrom2'] = $params[':dayOffFrom3'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][0]);
                $params[':dayOffTo1']   = $params[':dayOffTo2']   = $params[':dayOffTo3']   = $params[':dateTo']  =
                    DateTimeService::getCustomDateTimeObjectInUtc($criteria['dates'][1])->modify('+1 day')->format('Y-m-d H:i:s');
            }

            if (isset($criteria['type'])) {
                $where[] = 'do.type = :type';
                $params[':type'] = $criteria['type'];
            }

            if (!empty($criteria['providers'])) {
                $queryProviders = [];

                foreach ((array)$criteria['providers'] as $index => $value) {
                    $param = ':provider' . $index;

                    $queryProviders[] = $param;

                    $params[$param] = $value;
                }

                $where[] = '(do.userId IN (' . implode(', ', $queryProviders) . ') OR do.userId IS NULL)';
            }

            if (isset($criteria['providerId'])) {
                $where[] = '(do.userId = :providerId OR do.userId IS NULL)';
                $params[':providerId'] = $criteria['providerId'];
            }

            $userFields = '
                u.id AS user_id,
                u.firstName AS user_firstName,
                u.lastName AS user_lastName,
                u.email AS user_email,
                CONCAT(u.firstName, " ", u.lastName) AS user_fullName
            ';

            $userJoin = " LEFT JOIN {$userTable} u ON u.id = do.userId";

            $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $statement = $this->connection->prepare(
                "SELECT
                {$userFields},
                    do.id,
                    do.name,
                    do.userId,
                    do.startDate,
                    do.endDate
                FROM {$this->table} do
                {$userJoin}
                {$where}"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        return call_user_func([BlockTimeFactory::class, 'createCollection'], $rows);
    }

    /**
     * @param $id
     * @return mixed
     * @throws QueryExecutionException
     */
    public function getBlockTimeById($id)
    {
        $params[':id'] = $id;

        try {
            $statement = $this->connection->prepare(
                "SELECT
                    do.id,
                    do.name,
                    do.userId,
                    do.startDate,
                    do.endDate
                FROM {$this->table} do
                WHERE do.id = :id"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        return call_user_func([BlockTimeFactory::class, 'createCollection'], $rows)->getItem($id);
    }
}
