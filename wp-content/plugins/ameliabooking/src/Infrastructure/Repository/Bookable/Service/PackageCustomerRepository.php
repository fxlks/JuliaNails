<?php

namespace AmeliaBooking\Infrastructure\Repository\Bookable\Service;

use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Entity\Bookable\Service\Package;
use AmeliaBooking\Domain\Entity\Bookable\Service\PackageCustomer;
use AmeliaBooking\Domain\Factory\Bookable\Service\PackageCustomerFactory;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\PackagesCustomersServicesTable;
use AmeliaBooking\Infrastructure\Connection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\PackagesServicesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\PackagesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\ServicesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\AppointmentsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\CustomerBookingsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Coupon\CouponsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Payment\PaymentsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\UsersTable;

class PackageCustomerRepository extends AbstractRepository
{
    public const FACTORY = PackageCustomerFactory::class;

    /** @var string */
    protected $packagesCustomersServicesTable;

    /**
     * @param Connection $connection
     * @param string     $table
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        Connection $connection,
        $table
    ) {
        parent::__construct($connection, $table);

        $this->packagesCustomersServicesTable = PackagesCustomersServicesTable::getTableName();
    }

    /**
     * @param PackageCustomer $entity
     *
     * @return int
     * @throws QueryExecutionException
     */
    public function add($entity)
    {
        $data = $entity->toArray();

        $params = [
            ':packageId'        => $data['packageId'],
            ':customerId'       => $data['customerId'],
            ':price'            => $data['price'],
            ':tax'              => !empty($data['tax']) ? json_encode($data['tax']) : null,
            ':start'            => $data['start'],
            ':end'              => $data['end'],
            ':purchased'        => $data['purchased'],
            ':bookingsCount'    => $data['bookingsCount'],
            ':couponId'         => $data['couponId'],
            ':token'            => $data['token'] ?: null,
        ];

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO {$this->table}
                (`packageId`, `customerId`, `price`, `tax`, `start`, `end`, `purchased`, `status`, `bookingsCount`, `couponId`, `token`)
                VALUES
                (:packageId, :customerId, :price, :tax, :start, :end, :purchased, 'approved', :bookingsCount, :couponId, :token)"
            );

            $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $this->connection->lastInsertId();
    }


    /**
     * @param int            $id
     * @param PackageCustomer $entity
     *
     * @return boolean
     * @throws QueryExecutionException
     */
    public function update($id, $entity)
    {
        $data = $entity->toArray();

        $params = [
            ':status' => $data['status'],
            ':end'    => $data['end'],
            ':id'     => $id,
        ];


        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table}
                SET
                `status` = :status,
                `end` = :end
                WHERE
                id = :id"
            );

            $statement->execute($params);

            return true;
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to save data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param Package $package
     * @param int $customerId
     * @param array $limitPerCustomer
     * @param boolean $packageSpecific
     * @return int
     * @throws QueryExecutionException
     */
    public function getUserPackageCount($package, $customerId, $limitPerCustomer, $packageSpecific)
    {
        $params = [
            ':customerId' => $customerId
        ];

        $startDate = DateTimeService::getNowDateTimeObject()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i');

        $intervalString = "interval " . $limitPerCustomer['period'] . " " . $limitPerCustomer['timeFrame'];

        $where = "(STR_TO_DATE('" . $startDate . "', '%Y-%m-%d %H:%i:%s') BETWEEN " .
            "(pc.purchased - " . $intervalString . " + interval 1 second) AND " .
            "(pc.purchased + " . $intervalString . " - interval 1 second))";  //+ interval 2 day

        if ($packageSpecific) {
            $where .= " AND pc.packageId = :packageId";
            $params[':packageId'] = $package->getId()->getValue();
        }

        try {
            $statement = $this->connection->prepare(
                "SELECT COUNT(DISTINCT pc.id) AS count
                    FROM {$this->table} pc
                    WHERE pc.customerId = :customerId AND {$where} AND pc.status = 'approved'
                "
            );

            $statement->execute($params);

            $rows = $statement->fetch()['count'];
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $rows;
    }

    /**
     * @param array $criteria
     *
     * @return array
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function getFilteredIds($criteria = [], $itemsPerPage = null)
    {
        $bookingsTable     = CustomerBookingsTable::getTableName();
        $appointmentsTable = AppointmentsTable::getTableName();
        $usersTable        = UsersTable::getTableName();
        $packagesTable     = PackagesTable::getTableName();

        $params = [];
        $where  = [];
        $joins  = '';
        $having = '';

        if (!empty($criteria['dates'])) {
            $where[] = "(pc.purchased BETWEEN :purchasedFrom AND :purchasedTo)";

            $params[':purchasedFrom'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][0]);

            $params[':purchasedTo'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][1]);
        }

        if (!empty($criteria['packages'])) {
            $queryServices = [];

            foreach ($criteria['packages'] as $index => $value) {
                $param = ':package' . $index;

                $queryServices[] = $param;

                $params[$param] = $value;
            }

            $where[] = 'pc.packageId IN (' . implode(', ', $queryServices) . ')';
        }


        if (!empty($criteria['customers'])) {
            $queryCustomers = [];

            foreach ($criteria['customers'] as $index => $value) {
                $param = ':customer' . $index;

                $queryCustomers[] = $param;

                $params[$param] = $value;
            }

            $where[] = 'pc.customerId IN (' . implode(', ', $queryCustomers) . ')';
        }


        if (!empty($criteria['search'])) {
            $terms = preg_split('/\s+/', trim($criteria['search']));
            $termIndex = 0;

            foreach ($terms as $term) {
                $param = ":search{$termIndex}";
                $params[$param] = "%{$term}%";

                $where[] = "(
                        p.name LIKE {$param}
                        OR u.firstName LIKE {$param}
                        OR u.lastName LIKE {$param}
                        OR pc.id LIKE {$param}
                    )";

                $termIndex++;
            }

            $joins .= "
                INNER JOIN {$usersTable} u ON u.id = pc.customerId
                INNER JOIN {$packagesTable} p ON p.id = pc.packageId
            ";
        }


        if (!empty($criteria['status'])) {
            $whereOr = [];
            foreach ($criteria['status'] as $status) {
                switch ($status) {
                    case 'expired':
                        $whereOr[] = "(pc.end IS NOT NULL AND pc.end < NOW())";
                        break;
                    case 'approved':
                    case 'active':
                        $whereOr[] = "((pc.end > NOW() OR pc.end IS NULL) AND pc.status = 'approved')";
                        break;
                    case 'canceled':
                        $whereOr[] = "(pc.status = 'canceled')";
                        break;
                    default:
                        break;
                }
            }
            $where[] = '(' . implode(' OR ', $whereOr) . ')';
        }

        if (!empty($criteria['providers']) || !empty($criteria['services']) || !empty($criteria['availability']) || !empty($criteria['locations'])) {
            $whereProviders = '';
            if (!empty($criteria['providers'])) {
                $queryProviders = [];

                foreach ($criteria['providers'] as $index => $value) {
                    $param = ':provider' . $index;

                    $queryProviders[] = $param;

                    $params[$param] = $value;
                }

                $whereProviders = 'a.providerId IN (' . implode(', ', $queryProviders) . ')';
            }

            $whereServices = '';
            $queryServices = [];
            if (!empty($criteria['services'])) {
                foreach ($criteria['services'] as $index => $value) {
                    $param = ':service' . $index;

                    $queryServices[] = $param;

                    $params[$param] = $value;
                }

                $whereServices = 'a.serviceId IN (' . implode(', ', $queryServices) . ')';
            }

            $whereLocations = '';
            if (!empty($criteria['locations'])) {
                $queryLocations = [];

                foreach ($criteria['locations'] as $index => $value) {
                    $param = ':location' . $index;

                    $queryLocations[] = $param;

                    $params[$param] = $value;
                }

                $whereLocations = 'a.locationId IN (' . implode(', ', $queryLocations) . ')';
            }

            if (!empty($criteria['availability']) && count($criteria['availability']) === 1) {
                if ($criteria['availability'][0] === 'full') {
                    $having = "HAVING COUNT(a.id)>0 AND
                   (
                       COUNT(a.id) = (
                      		SELECT SUM(pcs2.bookingsCount) FROM {$this->packagesCustomersServicesTable} pcs2 WHERE pcs2.packageCustomerId = pc.id
                   		)
                       OR COUNT(a.id) = pc.bookingsCount
                    )";
                } elseif ($criteria['availability'][0] === 'available') {
                    $having = "HAVING 
                   (pc.bookingsCount = 0 AND COUNT(a.id) < (
                      		SELECT SUM(pcs2.bookingsCount) FROM {$this->packagesCustomersServicesTable} pcs2 WHERE pcs2.packageCustomerId=pc.id
                   		)
                    )
                    OR (pc.bookingsCount > 0 AND COUNT(a.id) < pc.bookingsCount)";
                }

                if (!empty($whereServices)) {
                    $where[] = "EXISTS (
                        SELECT a2.id
                        FROM {$appointmentsTable} a2 
                        INNER JOIN {$bookingsTable} cb2 ON a2.id = cb2.appointmentId
                        INNER join {$this->packagesCustomersServicesTable} pcs2 on pcs2.id = cb2.packageCustomerServiceId and pcs2.packageCustomerId = pc.id
                                      
                        WHERE a2.serviceId IN (" . implode(', ', $queryServices) . "))";
                }
                if (!empty($whereProviders)) {
                    $where[] = "EXISTS (
                        SELECT a2.id
                        FROM {$appointmentsTable} a2 
                        INNER JOIN {$bookingsTable} cb2 ON a2.id = cb2.appointmentId
                        INNER join {$this->packagesCustomersServicesTable} pcs2 on pcs2.id = cb2.packageCustomerServiceId and pcs2.packageCustomerId = pc.id
                                      
                        WHERE a2.providerId IN (" . implode(', ', $queryProviders) . "))";
                }
                if (!empty($whereLocations)) {
                    $where[] = "EXISTS (
                        SELECT a2.id
                        FROM {$appointmentsTable} a2 
                        INNER JOIN {$bookingsTable} cb2 ON a2.id = cb2.appointmentId
                        INNER join {$this->packagesCustomersServicesTable} pcs2 on pcs2.id = cb2.packageCustomerServiceId and pcs2.packageCustomerId = pc.id
                                      
                        WHERE a2.locationId IN (" . implode(', ', $queryLocations) . "))";
                }
            } else {
                if (!empty($whereServices)) {
                    $where[] = $whereServices;
                }
                if (!empty($whereProviders)) {
                    $where[] = $whereProviders;
                }
                if (!empty($whereLocations)) {
                    $where[] = $whereLocations;
                }
            }

            $joins .= "
                INNER JOIN {$this->packagesCustomersServicesTable} pcs ON pc.id = pcs.packageCustomerId
                LEFT JOIN {$bookingsTable} cb ON pcs.id = cb.packageCustomerServiceId
                LEFT JOIN {$appointmentsTable} a ON a.id = cb.appointmentId
                ";
        }


        if (isset($criteria['couponId'])) {
            $where[]                = 'pc.couponId = :couponId';
            $params[':couponId']    = (int)$criteria['couponId'];
        }

        $limit = $this->getLimit(
            !empty($criteria['page']) ? (int)$criteria['page'] : 0,
            !empty($itemsPerPage) ? (int)$itemsPerPage : 0
        );

        $orderBy = 'ORDER BY pc.purchased';
        if (!empty($criteria['sort'])) {
            $column      = $criteria['sort'][0] === '-' ? substr($criteria['sort'], 1) : $criteria['sort'];
            $orderColumn = '';
            if ($column === 'customer') {
                $joins      .= "
                    INNER JOIN {$usersTable} cu ON cu.id = pc.customerId
                ";
                $orderColumn = 'CONCAT(cu.firstName, " ", cu.lastName)';
            } elseif ($column === 'date') {
                $orderColumn = 'pc.purchased';
            } elseif ($column === 'id') {
                $orderColumn = 'pc.id';
            } elseif ($column === 'package') {
                $joins      .= "
                    INNER JOIN {$packagesTable} pa ON pa.id = pc.packageId
                ";
                $orderColumn = 'pa.name';
            }
            $orderDir = $orderColumn ? ($criteria['sort'][0] === '-' ? 'DESC' : 'ASC') : '';
            $orderBy  = $orderColumn ? "ORDER BY {$orderColumn} {$orderDir}" : 'ORDER BY pc.purchased';
        }

        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        try {
            $statement = $this->connection->prepare(
                "SELECT 
                    pc.id AS id,
                    pc.bookingsCount AS bookingsCount
                FROM {$this->table} pc
                {$joins}
                {$where}
                GROUP BY pc.id
                {$having}
                {$orderBy}
                {$limit}"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return array_column($rows, 'id');
    }

    /**
     * @param array $ids
     * @param array $options
     * @param string $sort
     *
     * @return Collection
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function getFiltered($ids, $options = [], $sort = null)
    {
        $bookingsTable = CustomerBookingsTable::getTableName();

        $appointmentsTable = AppointmentsTable::getTableName();

        $usersTable           = UsersTable::getTableName();
        $packagesTable        = PackagesTable::getTableName();
        $packageServicesTable = PackagesServicesTable::getTableName();
        $servicesTable        = ServicesTable::getTableName();
        $paymentsTable        = PaymentsTable::getTableName();
        $couponsTable         = CouponsTable::getTableName();

        $params = [];

        $where = [];

        $fields = "";

        $joins = "
                INNER JOIN {$this->packagesCustomersServicesTable} pcs ON pc.id = pcs.packageCustomerId
                LEFT JOIN {$bookingsTable} cb ON pcs.id = cb.packageCustomerServiceId
                LEFT JOIN {$appointmentsTable} a ON a.id = cb.appointmentId
                LEFT JOIN {$usersTable} cu ON cu.id = pc.customerId
                LEFT JOIN {$packagesTable} pa ON pa.id = pc.packageId
                LEFT JOIN {$paymentsTable} p ON p.packageCustomerId = pc.id
                LEFT JOIN {$couponsTable} c ON c.id = pc.couponId
                ";

        if (!empty($options['fetchPackageServices'])) {
            $joins .= "
                LEFT JOIN {$packageServicesTable} pas ON pas.packageId = pa.id
                LEFT JOIN {$servicesTable} s ON s.id = pas.serviceId
            ";

            $fields .= "
                pas.id AS package_service_id,
                pas.serviceId AS package_service_serviceId,
                
                s.id AS service_id,
                s.name AS service_name,
            ";
        }

        if (!empty($options['fetchAppointmentProviders'])) {
            $joins .= "
                LEFT JOIN {$usersTable} pu ON pu.id = a.providerId
            ";

            $fields .= "
                pu.id AS provider_id,
                pu.firstName AS provider_firstName,
                pu.lastName AS provider_lastName,
                pu.email AS provider_email,
                pu.badgeId AS provider_badgeId,
                pu.pictureFullPath AS provider_pictureFullPath,
                pu.pictureThumbPath AS provider_pictureThumbPath,
            ";
        }

        if (!empty($ids)) {
            $queryIds = [];

            foreach ($ids as $index => $value) {
                $param = ':id' . $index;

                $queryIds[] = $param;

                $params[$param] = $value;
            }

            $where[] = 'pc.id IN (' . implode(', ', $queryIds) . ')';
        }

        $orderBy = 'ORDER BY pc.purchased';
        if ($sort) {
            $column      = $sort[0] === '-' ? substr($sort, 1) : $sort;
            $orderColumn = '';
            if ($column === 'customer') {
                $orderColumn = 'CONCAT(cu.firstName, " ", cu.lastName)';
            } elseif ($column === 'date') {
                $orderColumn = 'pc.purchased';
            } elseif ($column === 'id') {
                $orderColumn = 'pc.id';
            } elseif ($column === 'package') {
                $orderColumn = 'pa.name';
            }
            $orderDir = $orderColumn ? ($sort[0] === '-' ? 'DESC' : 'ASC') : '';
            $orderBy  = $orderColumn ? "ORDER BY {$orderColumn} {$orderDir}" : 'ORDER BY pc.purchased';
        }

        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $fields .= "
            pc.id AS package_customer_id,
            pc.packageId AS package_customer_packageId,
            pc.purchased AS package_customer_purchased,
            pc.end AS package_customer_end,
            pc.status AS package_customer_status,
            pc.customerId AS package_customer_customerId,
            pc.bookingsCount AS package_customer_bookingsCount,
            pc.price AS package_customer_price,
            pc.tax AS package_customer_tax,
            pc.couponId AS package_customer_couponId,
            pc.token AS package_customer_token,
            
            pcs.id AS package_customer_service_id,
            pcs.bookingsCount AS package_customer_service_bookingsCount,
            
            cb.id AS booking_id,
                
            a.id AS appointment_id,
            a.providerId AS appointment_providerId,
            a.serviceId AS appointment_serviceId,
            a.notifyParticipants AS appointment_notifyParticipants,
            a.bookingStart AS appointment_bookingStart,
            a.bookingEnd AS appointment_bookingEnd,
            a.status AS appointment_status,
            
            cu.id AS customer_id,
            cu.firstname AS customer_firstName,
            cu.lastname AS customer_lastName,
            cu.email AS customer_email,
            cu.note AS customer_note,
            
            pa.id AS package_id,
            pa.name AS package_name,
            pa.pictureThumbPath AS package_pictureThumbPath,
            pa.pictureFullPath AS package_pictureFullPath,
            pa.color AS package_color,
            pa.calculatedPrice AS package_calculatedPrice,
            pa.discount AS package_discount,
            
            p.id AS payment_id,
            p.status AS payment_status,
            p.amount AS payment_amount,
            p.dateTime AS payment_dateTime,
            p.gateway AS payment_gateway,
            p.wcOrderId AS payment_wcOrderId,
            p.wcOrderItemId AS payment_wcOrderItemId,
            p.created AS payment_created,
            
            c.id AS coupon_id,
            c.discount AS coupon_discount,
            c.deduction AS coupon_deduction,
            c.status AS coupon_status
        ";

        try {
            $statement = $this->connection->prepare(
                "SELECT {$fields}
                FROM {$this->table} pc
                {$joins}
                {$where}
                {$orderBy}
                "
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return call_user_func([static::FACTORY, 'createCollection'], $rows);
    }
}
