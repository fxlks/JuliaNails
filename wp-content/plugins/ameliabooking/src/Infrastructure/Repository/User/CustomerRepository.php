<?php

namespace AmeliaBooking\Infrastructure\Repository\User;

use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Repository\User\CustomerRepositoryInterface;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Domain\ValueObjects\String\Status;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\PackagesCustomersTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\AppointmentsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\CustomerBookingsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\CustomerBookingsToEventsPeriodsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\EventsPeriodsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\WPUsersTable;

/**
 * Class UserRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository
 */
class CustomerRepository extends UserRepository implements CustomerRepositoryInterface
{
    /**
     * @param     $criteria
     * @param int $itemsPerPage
     *
     * @return array
     * @throws QueryExecutionException
     * @throws \Exception
     */
    public function getFiltered($criteria, $itemsPerPage = null)
    {
        try {
            $wpUserTable       = WPUsersTable::getTableName();
            $bookingsTable     = CustomerBookingsTable::getTableName();
            $appointmentsTable = AppointmentsTable::getTableName();
            $eventsPeriodsTable = EventsPeriodsTable::getTableName();
            $bookingsEventsPeriodsTable = CustomerBookingsToEventsPeriodsTable::getTableName();
            $packagesCustomersTable = PackagesCustomersTable::getTableName();

            $params = [
                ':type_customer'        => AbstractUser::USER_ROLE_CUSTOMER,
                ':type_admin'           => AbstractUser::USER_ROLE_ADMIN,
            ];

            $joinWithBookings = empty($criteria['ignoredBookings']);

            $where = [
                'u.type IN (:type_customer, :type_admin)',
            ];

            $order = '';
            if (!empty($criteria['sort'])) {
                $column      = $criteria['sort'][0] === '-' ? substr($criteria['sort'], 1) : $criteria['sort'];
                $orderColumn = $column === 'customer' ? 'CONCAT(u.firstName, " ", u.lastName)' : 'lastBooking';
                $orderDirection = $criteria['sort'][0] === '-' ? 'DESC' : 'ASC';
                $order          = "ORDER BY {$orderColumn} {$orderDirection}";

                $joinWithBookings = $column !== 'customer' || $joinWithBookings;
            }

            if (!empty($criteria['search'])) {
                $terms = preg_split('/\s+/', trim($criteria['search']));
                $termIndex = 0;

                foreach ($terms as $term) {
                    $param = ":search{$termIndex}";
                    $params[$param] = "%{$term}%";

                    $where[] = "(
                        u.firstName LIKE {$param}
                        OR u.lastName LIKE {$param}
                        OR u.email LIKE {$param}
                        OR u.phone LIKE {$param}
                        OR u.note LIKE {$param}
                        OR wpu.display_name LIKE {$param}
                        OR u.id LIKE {$param}
                    )";

                    $termIndex++;
                }
            }

            if (!empty($criteria['customers'])) {
                $customersCriteria = [];

                foreach ((array)$criteria['customers'] as $key => $customerId) {
                    $params[":customerId$key"] = $customerId;
                    $customersCriteria[]       = ":customerId$key";
                }

                $where[] = 'u.id IN (' . implode(', ', $customersCriteria) . ')';
            }

            $statsFields = '
                NULL as lastBooking,
                NULL as lastAppointment,
                NULL as lastEvent,
                0 as totalBookings,
                0 as countPendingAppointments,
                0 as countAppointmentBookings,
                0 as countEventBookings,
            ';

            $statsJoins = '';

            $having = '';

            if ($joinWithBookings) {
                $params[':bookingPendingStatus'] = BookingStatus::PENDING;

                $statsFields = "
                    COALESCE(GREATEST(MAX(app.bookingStart), MAX(ep.periodStart)),
                     MAX(app.bookingStart), MAX(ep.periodStart), MAX(pc.purchased)) as lastBooking,
                    MAX(app.bookingStart) as lastAppointment,
                    MAX(ep.periodStart) as lastEvent,
                    MAX(pc.purchased) as lastPackage,
                    COUNT(DISTINCT cb.id) as totalBookings,
                    SUM(case when cb.status = :bookingPendingStatus then 1 else 0 end) as countPendingAppointments,
                    COUNT(DISTINCT CASE WHEN cb.appointmentId IS NOT NULL THEN cb.id ELSE NULL END) as countAppointmentBookings,
                    COUNT(DISTINCT CASE WHEN cb.appointmentId IS NULL THEN cb.id ELSE NULL END) as countEventBookings,
                    COUNT(pc.customerId) as countPackagePurchases,
                ";

                $statsJoins = "
                    LEFT JOIN {$bookingsTable} cb ON u.id = cb.customerId
                    LEFT JOIN {$appointmentsTable} app ON app.id = cb.appointmentId
                    LEFT JOIN {$bookingsEventsPeriodsTable} bep ON bep.customerBookingId = cb.id
                    LEFT JOIN {$eventsPeriodsTable} ep ON ep.id = bep.eventPeriodId
                    LEFT JOIN {$packagesCustomersTable} pc ON pc.customerId = u.id
                ";

                if (!empty($criteria['noShow'])) {
                    $having = "HAVING (";
                    foreach ($criteria['noShow'] as $index => $noShowId) {
                        $param = ':noShow' . $index;
                        $params[$param] = $noShowId;
                        $having .= ($index === 0 ? "" : " OR ") . "(COUNT(DISTINCT CASE WHEN cb.status = 'no-show' THEN cb.id ELSE NULL END) " .
                            ($noShowId === "3" ? '>=' : '=') . " " . $param . ")";
                    }
                    $having .= ")";
                }
            }

            $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $limit = $this->getLimit(
                !empty($criteria['page']) ? (int)$criteria['page'] : 0,
                (int)$itemsPerPage
            );

            $statement = $this->connection->prepare(
                "SELECT 
                u.id as id,
                u.status as status,
                u.firstName as firstName,
                u.lastName as lastName,
                u.email as email,
                u.phone as phone,
                u.countryPhoneIso AS countryPhoneIso,
                u.gender as gender,
                u.externalId as externalId,
                u.translations as translations,
                IF(u.birthday IS NOT NULL, u.birthday , '') as birthday,
                u.note as note,
                u.customFields as customFields,
                {$statsFields}
                IF(wpu.display_name IS NOT NULL, wpu.display_name , '') as wpName
                FROM {$this->table} as u
                LEFT JOIN {$wpUserTable} wpu ON u.externalId = wpu.id
                {$statsJoins}
                {$where}
                GROUP BY u.id
                {$having}
                {$order}
                {$limit}"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        $items = [];
        foreach ($rows as $row) {
            $row['id']         = (int)$row['id'];
            $row['externalId'] = $row['externalId'] === null ? $row['externalId'] : (int)$row['externalId'];
            $row['lastBooking'] = !empty($row['lastBooking']) ? DateTimeService::getCustomDateTimeFromUtc($row['lastBooking']) : $row['lastBooking'];
            $row['lastAppointment'] = !empty($row['lastAppointment']) ?
                DateTimeService::getCustomDateTimeFromUtc($row['lastAppointment']) :
                $row['lastAppointment'];
            $row['lastEvent'] = !empty($row['lastEvent']) ? DateTimeService::getCustomDateTimeFromUtc($row['lastEvent']) : $row['lastEvent'];
            $row['lastPackage'] = !empty($row['lastPackage']) ? DateTimeService::getCustomDateTimeFromUtc($row['lastPackage']) : null;

            $row['totalBookings'] = (int)$row['totalBookings'];
            $row['totalAppointments'] = (int)$row['countAppointmentBookings'];
            $row['totalEvents'] = (int)$row['countEventBookings'];
            $row['totalPackages'] = !empty($row['countPackagePurchases']) ? (int)$row['countPackagePurchases'] : 0;

            // Fix for customFields being encoded multiple times
            if ($row['customFields'] && !is_array(json_decode($row['customFields'], true))) {
                $row['customFields'] = null;
            }

            $items[$row['id']] = $row;
        }

        return $items;
    }

    /**
     * @param $criteria
     *
     * @return mixed
     * @throws QueryExecutionException
     */
    public function getCount($criteria)
    {
        $wpUserTable = WPUsersTable::getTableName();

        $params = [
            ':type_customer' => AbstractUser::USER_ROLE_CUSTOMER,
            ':type_admin'    => AbstractUser::USER_ROLE_ADMIN,
        ];

        $where = [
            'u.type IN (:type_customer, :type_admin)',
        ];

        if (!empty($criteria['search'])) {
            $terms = preg_split('/\s+/', trim($criteria['search']));
            $termIndex = 0;

            foreach ($terms as $term) {
                $param = ":search{$termIndex}";
                $params[$param] = "%{$term}%";

                $where[] = "(
                        u.firstName LIKE {$param}
                        OR u.lastName LIKE {$param}
                        OR u.email LIKE {$param}
                        OR u.phone LIKE {$param}
                        OR u.note LIKE {$param}
                        OR wpu.display_name LIKE {$param}
                        OR u.id LIKE {$param}
                    )";

                $termIndex++;
            }
        }

        if (!empty($criteria['customers'])) {
            $customersCriteria = [];

            foreach ((array)$criteria['customers'] as $key => $customerId) {
                $params[":customerId$key"] = $customerId;
                $customersCriteria[]       = ":customerId$key";
            }

            $where[] = 'u.id IN (' . implode(', ', $customersCriteria) . ')';
        }

        if (!empty($criteria['noShow'])) {
            $bookingsTable = CustomerBookingsTable::getTableName();

            $noShowWhere = "exists (SELECT COUNT(*) as c FROM {$bookingsTable} cb WHERE cb.status='no-show' AND cb.customerId=u.id HAVING ";

            foreach ($criteria['noShow'] as $index => $noShowId) {
                $param = ':noShow' . $index;
                $params[$param] = $noShowId;
                $noShowWhere .= ($index === 0 ? "" : " OR ") . "c " . ($noShowId === "3" ? '>=' : '=') . $param;
            }
            $noShowWhere .= ")";

            $where[] = $noShowWhere;
        }

        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        try {
            $statement = $this->connection->prepare(
                "SELECT COUNT(*) as count
                FROM {$this->table} as u 
                LEFT JOIN {$wpUserTable} wpu ON u.externalId = wpu.id
                $where
                "
            );

            $statement->execute($params);

            $rows = $statement->fetch()['count'];
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $rows;
    }

    /**
     * @param string $phone
     *
     * @return array
     * @throws QueryExecutionException
     * @throws \Exception
     */
    public function getByPhoneNumber($phone)
    {
        try {
            $params[':phone'] = '+' . $phone;

            $statement = $this->connection->prepare(
                "SELECT 
                u.id as id,
                u.status as status,
                u.firstName as firstName,
                u.lastName as lastName,
                u.email as email,
                u.phone as phone,
                u.countryPhoneIso AS countryPhoneIso,
                u.gender as gender,
                u.externalId as externalId,
                IF(u.birthday IS NOT NULL, u.birthday , '') as birthday,
                u.note as note 
                FROM {$this->table} as u
                WHERE u.type = 'customer' AND phone = :phone"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $rows;
    }

    /**
     * @param array $criteria
     *
     * @return Collection
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function getByCriteria($criteria = [])
    {
        $params = [];

        $where = [];

        $fields = '
            u.id AS id,
            u.type AS type,
            u.firstName AS firstName,
            u.lastName AS lastName,
            u.email AS email,
            u.note AS note,
            u.phone AS phone,
            u.countryPhoneIso AS countryPhoneIso,
            u.gender AS gender,
            u.birthday AS birthday,
            u.status AS status
        ';

        if (!empty($criteria['ids'])) {
            $queryIds = [];

            foreach ($criteria['ids'] as $index => $value) {
                $param = ':id' . $index;

                $queryIds[] = $param;

                $params[$param] = $value;
            }

            $where[] = 'u.id IN (' . implode(', ', $queryIds) . ')';
        }

        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        try {
            $statement = $this->connection->prepare(
                "SELECT
                {$fields}
                FROM {$this->table} u
                {$where}"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find event by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        $items = new Collection();

        foreach ($rows as $row) {
            $row['type'] = 'customer';

            $items->addItem(call_user_func([static::FACTORY, 'create'], $row), $row['id']);
        }

        return $items;
    }
}
