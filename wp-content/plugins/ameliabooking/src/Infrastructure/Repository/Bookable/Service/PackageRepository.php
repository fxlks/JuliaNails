<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Repository\Bookable\Service;

use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\Service\Package;
use AmeliaBooking\Domain\Factory\Bookable\Service\PackageFactory;
use AmeliaBooking\Infrastructure\Connection;
use AmeliaBooking\Infrastructure\DB\WPDB\Statement;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\PackagesServicesLocationsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\PackagesServicesProvidersTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\PackagesServicesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\ServicesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Gallery\GalleriesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Location\LocationsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\Provider\ProvidersServiceTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\UsersTable;

/**
 * Class PackageRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\Service
 */
class PackageRepository extends AbstractRepository
{
    public const FACTORY = PackageFactory::class;

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
     * @param Package $entity
     *
     * @return int
     * @throws QueryExecutionException
     */
    public function add($entity)
    {
        $data = $entity->toArray();

        $params = [
            ':name'             => $data['name'],
            ':description'      => $data['description'],
            ':color'            => $data['color'],
            ':price'            => $data['price'],
            ':status'           => $data['status'],
            ':pictureFullPath'  => $data['pictureFullPath'],
            ':pictureThumbPath' => $data['pictureThumbPath'],
            ':position'         => $data['position'],
            ':calculatedPrice'  => $data['calculatedPrice'] ? 1 : 0,
            ':discount'         => $data['discount'],
            ':settings'         => $data['settings'],
            ':endDate'          => $data['endDate'],
            ':durationCount'    => $data['durationCount'],
            ':durationType'     => $data['durationType'],
            ':translations'     => $data['translations'],
            ':deposit'          => $data['deposit'],
            ':depositPayment'   => $data['depositPayment'],
            ':fullPayment'      => $data['fullPayment'] ? 1 : 0,
            ':sharedCapacity'   => $data['sharedCapacity'] ? 1 : 0,
            ':quantity'         => $data['quantity'],
            ':limitPerCustomer' => $data['limitPerCustomer']
        ];

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO 
                {$this->table} 
                (
                `name`, 
                `description`, 
                `color`, 
                `price`, 
                `status`, 
                `pictureFullPath`,
                `pictureThumbPath`,
                `calculatedPrice`,
                `discount`,
                `position`,
                `settings`,
                `endDate`,
                `durationCount`,
                `durationType`,
                `translations`,
                `deposit`,
                `depositPayment`,
                `fullPayment`,
                `sharedCapacity`,
                `quantity`,
                `limitPerCustomer`
                ) VALUES (
                :name,
                :description,
                :color,
                :price,
                :status,
                :pictureFullPath,
                :pictureThumbPath,
                :calculatedPrice,
                :discount,
                :position,
                :settings,
                :endDate,
                :durationCount,
                :durationType,
                :translations,
                :deposit,
                :depositPayment,
                :fullPayment,
                :sharedCapacity,
                :quantity,
                :limitPerCustomer
                )"
            );

            $statement->execute($params);

            return $this->connection->lastInsertId();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int     $packageId
     * @param Package $entity
     *
     * @throws QueryExecutionException
     */
    public function update($packageId, $entity)
    {
        $data = $entity->toArray();

        $params = [
            ':name'             => $data['name'],
            ':description'      => $data['description'],
            ':color'            => $data['color'],
            ':price'            => $data['price'],
            ':status'           => $data['status'],
            ':pictureFullPath'  => $data['pictureFullPath'],
            ':pictureThumbPath' => $data['pictureThumbPath'],
            ':position'         => $data['position'],
            ':calculatedPrice'  => $data['calculatedPrice'] ? 1 : 0,
            ':discount'         => $data['discount'],
            ':settings'         => $data['settings'],
            ':endDate'          => $data['endDate'],
            ':durationCount'    => $data['durationCount'],
            ':durationType'     => $data['durationType'],
            ':translations'     => $data['translations'],
            ':deposit'          => $data['deposit'],
            ':depositPayment'   => $data['depositPayment'],
            ':fullPayment'      => $data['fullPayment'] ? 1 : 0,
            ':sharedCapacity'   => $data['sharedCapacity'] ? 1 : 0,
            ':quantity'         => $data['quantity'],
            ':limitPerCustomer' => $data['limitPerCustomer'],
            ':id'               => $packageId
        ];


        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table}
                SET
                `name`              = :name,
                `description`       = :description,
                `color`             = :color,
                `price`             = :price,
                `status`            = :status,
                `pictureFullPath`   = :pictureFullPath,
                `pictureThumbPath`  = :pictureThumbPath,
                `position`          = :position,
                `calculatedPrice`   = :calculatedPrice,
                `discount`          = :discount,
                `settings`          = :settings,
                `endDate`           = :endDate,
                `durationCount`     = :durationCount,
                `durationType`      = :durationType,
                `translations`      = :translations,
                `deposit`           = :deposit,
                `depositPayment`    = :depositPayment,
                `fullPayment`       = :fullPayment,
                `sharedCapacity`    = :sharedCapacity,
                `quantity`          = :quantity, 
                `limitPerCustomer`  = :limitPerCustomer    
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
        $order = 'ORDER BY p.name, ps.position ASC, ps.id ASC';

        if (isset($criteria['sort'])) {
            if ($criteria['sort'] === '') {
                $order = 'ORDER BY p.position';
            } else {
                $sortField = $criteria['sort'];
                if (strpos($sortField, '-') === 0) {
                    $sortField = substr($sortField, 1);
                }
                switch ($sortField) {
                    case 'name':
                        $orderColumn = 'p.name';
                        break;
                    case 'price':
                        $orderColumn = 'p.price';
                        break;
                    case 'id':
                        $orderColumn = 'p.id';
                        break;
                    case 'services':
                        $packageServicesTable = PackagesServicesTable::getTableName();
                        $orderColumn = "(SELECT COUNT(*) FROM {$packageServicesTable} WHERE packageId = p.id)";
                        break;
                    default:
                        $orderColumn = 'p.name';
                }
                $orderDirection = $criteria['sort'][0] === '-' ? 'DESC' : 'ASC';
                $order = "ORDER BY {$orderColumn} {$orderDirection}";
            }
        }

        if (!empty($criteria['id'])) {
            $params[':id'] = $criteria['id'];
            $where[] = 'p.id = :id';
        }

        if (!empty($criteria['search'])) {
            $terms = preg_split('/\s+/', trim($criteria['search']));
            $termIndex = 0;

            foreach ($terms as $term) {
                $param = ":search{$termIndex}";
                $params[$param] = "%{$term}%";

                $where[] = "(
                        p.name LIKE {$param}
                        OR p.id LIKE {$param}
                    )";

                $termIndex++;
            }
        }

        if (!empty($criteria['services'])) {
            $queryServices = [];
            foreach ((array)$criteria['services'] as $index => $value) {
                $param = ':service' . $index;
                $queryServices[] = $param;
                $params[$param] = $value;
            }
            $where[] = 's.id IN (' . implode(', ', $queryServices) . ')';
        }

        if (!empty($criteria['packages'])) {
            $queryPackages = [];
            foreach ((array)$criteria['packages'] as $index => $value) {
                $param           = ':package' . $index;
                $queryPackages[] = $param;
                $params[$param]  = $value;
            }
            $where[] = 'p.id IN (' . implode(', ', $queryPackages) . ')';
        }

        if (!empty($criteria['status'])) {
            $params[':status'] = $criteria['status'];
            $where[] = 's.status = :status';
        }

        $whereSql = $where ? ' AND ' . implode(' AND ', $where) : '';
        $servicesTable = ServicesTable::getTableName();
        $usersTable = UsersTable::getTableName();
        $locationsTable = LocationsTable::getTableName();
        $packageServicesTable = PackagesServicesTable::getTableName();
        $providersToServicesTable = ProvidersServiceTable::getTableName();
        $packageServicesProvidersTable = PackagesServicesProvidersTable::getTableName();
        $packageServicesLocationsTable = PackagesServicesLocationsTable::getTableName();
        $galleriesTable = GalleriesTable::getTableName();

        // Define the common SELECT part of the query
        $selectSql = "SELECT
            p.id AS package_id,
            p.name AS package_name,
            p.description AS package_description,
            p.color AS package_color,
            p.price AS package_price,
            p.status AS package_status,
            p.pictureFullPath AS package_picture_full,
            p.pictureThumbPath AS package_picture_thumb,
            p.calculatedPrice AS package_calculated_price,
            p.discount AS package_discount,
            p.position AS package_position,
            p.settings AS package_settings,
            p.endDate AS package_endDate,
            p.durationCount AS package_durationCount,
            p.durationType AS package_durationType,
            p.translations AS package_translations,
            p.deposit AS package_deposit,
            p.depositPayment AS package_depositPayment,
            p.fullPayment AS package_fullPayment,
            p.sharedCapacity AS package_sharedCapacity,
            p.quantity AS package_quantity,
            p.limitPerCustomer AS package_limitPerCustomer,
            ps.id AS package_service_id,
            ps.quantity AS package_service_quantity,
            ps.minimumScheduled AS package_service_minimumScheduled,
            ps.maximumScheduled AS package_service_maximumScheduled,
            ps.allowProviderSelection AS package_service_allowProviderSelection,
            ps.position AS package_service_position,
            s.id AS service_id,
            s.price AS service_price,
            s.minCapacity AS service_minCapacity,
            s.maxCapacity AS service_maxCapacity,
            s.name AS service_name,
            s.description AS service_description,
            s.status AS service_status,
            s.categoryId AS service_categoryId,
            s.duration AS service_duration,
            s.timeBefore AS service_timeBefore,
            s.timeAfter AS service_timeAfter,
            s.pictureFullPath AS service_picture_full,
            s.pictureThumbPath AS service_picture_thumb,
            s.translations AS service_translations,
            s.show AS service_show,
            s.color AS service_color,
            l.id AS location_id,
            l.name AS location_name,
            l.address AS location_address,
            l.phone AS location_phone,
            l.latitude AS location_latitude,
            l.longitude AS location_longitude,
            COALESCE(psp.userId, pts.userId) AS provider_id,
            pu.firstName AS provider_firstName,
            pu.lastName AS provider_lastName,
            pu.email AS provider_email,
            pu.status AS provider_status,
            pu.translations AS provider_translations,
            pu.pictureFullPath AS provider_picture_full,
            pu.pictureThumbPath AS provider_picture_thumb,
            g.id AS gallery_id,
            g.pictureFullPath AS gallery_picture_full,
            g.pictureThumbPath AS gallery_picture_thumb,
            g.position AS gallery_position";

        $fromSql = "FROM {$this->table} p
            LEFT JOIN {$packageServicesTable} ps ON ps.packageId = p.id
            LEFT JOIN {$servicesTable} s ON ps.serviceId = s.id
            LEFT JOIN {$packageServicesProvidersTable} psp ON psp.packageServiceId = ps.id
            LEFT JOIN {$providersToServicesTable} pts ON pts.serviceId = ps.serviceId
            LEFT JOIN {$usersTable} pu ON pu.id = COALESCE(psp.userId, pts.userId)
            LEFT JOIN {$packageServicesLocationsTable} psl ON psl.packageServiceId = ps.id
            LEFT JOIN {$locationsTable} l ON l.id = psl.locationId
            LEFT JOIN {$galleriesTable} g ON g.entityId = p.id AND g.entityType = 'package'";

        try {
            // PAGINATION: If limit is set, use subquery for package IDs
            if (!empty($criteria['limit'])) {
                $itemsPerPage = (int)$criteria['limit'];
                $page = !empty($criteria['page']) ? (int)$criteria['page'] : 1;
                $offset = ($page - 1) * $itemsPerPage;

                // Get paginated package IDs
                $idSql = "SELECT DISTINCT p.id FROM {$this->table} p 
                    LEFT JOIN {$packageServicesTable} ps ON ps.packageId = p.id 
                    LEFT JOIN {$servicesTable} s ON ps.serviceId = s.id 
                    WHERE 1 = 1{$whereSql} {$order} 
                    LIMIT {$itemsPerPage} OFFSET {$offset}";

                $idStmt = $this->connection->prepare($idSql);
                $idStmt->execute($params);
                $packageIds = $idStmt->fetchAll(Statement::FETCH_COLUMN);

                if (empty($packageIds)) {
                    return call_user_func([static::FACTORY, 'createCollection'], []);
                }

                // Prepare ID parameters
                $inParams = [];
                foreach ($packageIds as $idx => $id) {
                    $inParams[":p_id{$idx}"] = $id;
                }
                $inClause = implode(',', array_keys($inParams));
                $params = array_merge($params, $inParams);

                $whereSqlWithIds = $whereSql ? $whereSql . ' AND ' : ' AND ';
                $whereSqlWithIds .= "p.id IN ($inClause)";

                $sql = "{$selectSql} {$fromSql} WHERE 1 = 1 {$whereSqlWithIds} {$order}";
                $statement = $this->connection->prepare($sql);
            } else {
                // Standard query without pagination
                $sql = "{$selectSql} {$fromSql} WHERE 1 = 1 {$whereSql} {$order}";
                $statement = $this->connection->prepare($sql);
            }

            $statement->execute($params);
            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
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

        $servicesTable = ServicesTable::getTableName();

        $usersTable = UsersTable::getTableName();

        $locationsTable = LocationsTable::getTableName();

        $packageServicesTable = PackagesServicesTable::getTableName();

        $packageServicesProvidersTable = PackagesServicesProvidersTable::getTableName();

        $packageServicesLocationsTable = PackagesServicesLocationsTable::getTableName();

        $galleriesTable = GalleriesTable::getTableName();

        try {
            $statement = $this->connection->prepare(
                "SELECT
                p.id AS package_id,
                p.name AS package_name,
                p.description AS package_description,
                p.color AS package_color,
                p.price AS package_price,
                p.status AS package_status,
                p.pictureFullPath AS package_picture_full,
                p.pictureThumbPath AS package_picture_thumb,
                p.calculatedPrice AS package_calculated_price,
                p.discount AS package_discount,
                p.position AS package_position,
                p.settings AS package_settings,
                p.endDate AS package_endDate,
                p.durationCount AS package_durationCount,
                p.durationType AS package_durationType,
                p.translations AS package_translations,
                p.deposit AS package_deposit,
                p.depositPayment AS package_depositPayment,
                p.fullPayment AS package_fullPayment,
                p.sharedCapacity AS package_sharedCapacity,
                p.quantity AS package_quantity,
                p.limitPerCustomer AS package_limitPerCustomer,
                
                ps.id AS package_service_id,
                ps.quantity AS package_service_quantity,
                ps.minimumScheduled AS package_service_minimumScheduled,
                ps.maximumScheduled AS package_service_maximumScheduled,
                ps.allowProviderSelection AS package_service_allowProviderSelection,
                ps.position AS package_service_position,
                                
                s.id AS service_id,
                s.price AS service_price,
                s.minCapacity AS service_minCapacity,
                s.maxCapacity AS service_maxCapacity,
                s.name AS service_name,
                s.status AS service_status,
                s.categoryId AS service_categoryId,
                s.duration AS service_duration,
                s.timeBefore AS service_timeBefore,
                s.timeAfter AS service_timeAfter,
                s.pictureFullPath AS service_picture_full,
                s.pictureThumbPath AS service_picture_thumb,
                s.show AS service_show,
                s.color AS service_color,
                
                l.id AS location_id,
                l.name AS location_name,
                l.address AS location_address,
                l.phone AS location_phone,
                l.latitude AS location_latitude,
                l.longitude AS location_longitude,

                pu.id AS provider_id,
                pu.firstName AS provider_firstName,
                pu.lastName AS provider_lastName,
                pu.email AS provider_email,
                pu.phone AS provider_phone,
                pu.countryPhoneIso AS provider_countryPhoneIso,
                pu.translations AS provider_translations,
                pu.stripeConnect AS provider_stripeConnect,
                                
                g.id AS gallery_id,
                g.pictureFullPath AS gallery_picture_full,
                g.pictureThumbPath AS gallery_picture_thumb,
                g.position AS gallery_position
                
                FROM {$this->table} p
                LEFT JOIN {$packageServicesTable} ps ON ps.packageId = p.id
                LEFT JOIN {$servicesTable} s ON ps.serviceId = s.id
                LEFT JOIN {$packageServicesProvidersTable} psp ON psp.packageServiceId = ps.id
                LEFT JOIN {$packageServicesLocationsTable} psl ON psl.packageServiceId = ps.id
                LEFT JOIN {$usersTable} pu ON pu.id = psp.userId
                LEFT JOIN {$locationsTable} l ON l.id = psl.locationId
                LEFT JOIN {$galleriesTable} g ON g.entityId = p.id AND g.entityType = 'package'
                WHERE p.id = :id"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return call_user_func([static::FACTORY, 'createCollection'], $rows)->getItem($id);
    }

    public function getCount($criteria)
    {
        $params = [];
        $where = [];

        // Only add filters if criteria is not empty
        if (!empty($criteria)) {
            if (!empty($criteria['search'])) {
                $terms = preg_split('/\s+/', trim($criteria['search']));
                $termIndex = 0;

                foreach ($terms as $term) {
                    $param = ":search{$termIndex}";
                    $params[$param] = "%{$term}%";

                    $where[] = "(
                        p.name LIKE {$param}
                        OR p.id LIKE {$param}
                    )";

                    $termIndex++;
                }
            }

            if (!empty($criteria['services'])) {
                $queryServices = [];
                foreach ((array)$criteria['services'] as $index => $value) {
                    $param = ':service' . $index;
                    $queryServices[] = $param;
                    $params[$param] = $value;
                }
                $where[] = "s.id IN (" . implode(', ', $queryServices) . ")";
            }
        }

        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $packageServicesTable = PackagesServicesTable::getTableName();
        $servicesTable = ServicesTable::getTableName();

        try {
            $statement = $this->connection->prepare(
                "SELECT COUNT(DISTINCT p.id) AS count 
                FROM {$this->table} p
                LEFT JOIN {$packageServicesTable} ps ON ps.packageId = p.id
                LEFT JOIN {$servicesTable} s ON ps.serviceId = s.id
                {$where}"
            );

            $statement->execute($params);

            $row = $statement->fetch()['count'];
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $row;
    }
}
