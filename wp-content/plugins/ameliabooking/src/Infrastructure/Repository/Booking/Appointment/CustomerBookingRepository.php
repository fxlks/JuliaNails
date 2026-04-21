<?php

namespace AmeliaBooking\Infrastructure\Repository\Booking\Appointment;

use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Appointment\CustomerBooking;
use AmeliaBooking\Domain\Factory\Booking\Appointment\CustomerBookingFactory;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\DB\WPDB\Statement;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\CustomerBookingsToEventsPeriodsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\CustomerBookingToEventsTicketsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\EventsPeriodsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\EventsProvidersTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\EventsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\PackagesCustomersServicesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Coupon\CouponsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Payment\PaymentsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\UsersTable;
use Exception;

/**
 * Class CustomerBookingRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\Booking\Appointment
 */
class CustomerBookingRepository extends AbstractRepository
{
    public const FACTORY = CustomerBookingFactory::class;

    /**
     * @param CustomerBooking $entity
     *
     * @return mixed
     * @throws QueryExecutionException
     */
    public function add($entity)
    {
        $data = $entity->toArray();

        $couponId = !empty($data['coupon']) ? $data['coupon']['id'] : null;
        if (!$couponId && !empty($data['couponId'])) {
            $couponId = $data['couponId'];
        }

        $params = [
            ':appointmentId'   => $data['appointmentId'],
            ':customerId'      => $data['customerId'],
            ':status'          => $data['status'],
            ':price'           => $data['price'],
            ':tax'             => !empty($data['tax']) ? json_encode($data['tax']) : null,
            ':persons'         => $data['persons'],
            ':couponId'        => $couponId,
            ':token'           => $data['token'],
            ':customFields'    => $data['customFields'] && json_decode($data['customFields']) !== false ?
                $data['customFields'] : null,
            ':info'            => $data['info'],
            ':aggregatedPrice' => $data['aggregatedPrice'] ? 1 : 0,
            ':utcOffset'       => $data['utcOffset'],
            ':packageCustomerServiceId' => !empty($data['packageCustomerService']['id']) ?
                $data['packageCustomerService']['id'] : null,
            ':duration'        => !empty($data['duration']) ? $data['duration'] : null,
            ':created'         => !empty($data['created']) ?
                DateTimeService::getCustomDateTimeInUtc($data['created']) : DateTimeService::getNowDateTimeInUtc(),
            ':actionsCompleted' => $data['actionsCompleted'] ? 1 : 0,
            ':qrCodes'         => $data['qrCodes'] && json_decode($data['qrCodes']) !== false ? $data['qrCodes'] : null,
        ];

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO {$this->table} 
                (
                `appointmentId`,
                `customerId`,
                `status`, 
                `price`,
                `tax`,
                `persons`,
                `couponId`, 
                `token`,
                `customFields`,
                `info`,
                `aggregatedPrice`,
                `utcOffset`,
                `packageCustomerServiceId`,
                `duration`,
                `created`,
                `actionsCompleted`,
                `qrCodes`
                )
                VALUES (
                :appointmentId, 
                :customerId, 
                :status, 
                :price,
                :tax, 
                :persons,
                :couponId,
                :token,
                :customFields,
                :info,
                :aggregatedPrice,
                :utcOffset,
                :packageCustomerServiceId,
                :duration,
                :created,
                :actionsCompleted,
                :qrCodes
                )"
            );

            $statement->execute($params);

            return $this->connection->lastInsertId();
        } catch (Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int             $id
     * @param CustomerBooking $entity
     *
     * @return mixed
     * @throws QueryExecutionException
     */
    public function update($id, $entity)
    {
        $data = $entity->toArray();

        $params = [
            ':id'           => $id,
            ':customerId'   => $data['customerId'],
            ':status'       => $data['status'],
            ':duration'     => !empty($data['duration']) ? $data['duration'] : null,
            ':persons'      => $data['persons'],
            ':couponId'     => !empty($data['coupon']) ? $data['coupon']['id'] : null,
            ':customFields' => $data['customFields'],
            ':qrCodes'      => $data['qrCodes'] && json_decode($data['qrCodes']) !== false ? $data['qrCodes'] : null,
        ];

        $updateColumns = "
            `customerId`   = :customerId,
            `status`       = :status,
            `duration`     = :duration,
            `persons`      = :persons,
            `couponId`     = :couponId,
            `customFields` = :customFields,
            `qrCodes`      = :qrCodes
        ";

        if (isset($data['utcOffset'])) {
            $params[':utcOffset'] = $data['utcOffset'];
            $updateColumns .= ",
                `utcOffset`    = :utcOffset";
        }

        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table} SET
                {$updateColumns}
                WHERE id = :id"
            );

            $statement->execute($params);

            return true;
        } catch (Exception $e) {
            throw new QueryExecutionException('Unable to save data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int             $id
     * @param CustomerBooking $entity
     *
     * @return mixed
     * @throws QueryExecutionException
     */
    public function updatePrice($id, $entity)
    {
        $data = $entity->toArray();

        $params = [
            ':id'           => $id,
            ':price'        => $data['price'],
        ];

        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table} SET
                `price`   = :price
                WHERE id = :id"
            );

            $statement->execute($params);

            return true;
        } catch (Exception $e) {
            throw new QueryExecutionException('Unable to save data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int             $id
     * @param CustomerBooking $entity
     *
     * @return bool
     * @throws QueryExecutionException
     */
    public function updateTax($id, $entity)
    {
        $data = $entity->toArray();

        $params = [
            ':id'  => $id,
            ':tax' => !empty($data['tax']) ? (is_array($data['tax']) ? json_encode($data['tax']) : $data['tax']) : null,
        ];

        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table} SET
                `tax`   = :tax
                WHERE id = :id"
            );

            $statement->execute($params);

            return true;
        } catch (Exception $e) {
            throw new QueryExecutionException('Unable to save data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Returns token for given id
     *
     * @param $id
     *
     * @return array
     * @throws QueryExecutionException
     */
    public function getToken($id)
    {
        try {
            $statement = $this->connection->prepare(
                "SELECT cb.token
                FROM {$this->table} cb
                WHERE cb.id = :id"
            );

            $statement->execute([':id' => $id]);

            $row = $statement->fetch();
        } catch (Exception $e) {
            throw new QueryExecutionException('Unable to return customer booking from' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $row;
    }

    /**
     * Returns tokens for given event id
     *
     * @param $id
     *
     * @return array
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function getTokensByEventId($id)
    {
        $eventsPeriodsTable = EventsPeriodsTable::getTableName();

        $customerBookingsEventsPeriods = CustomerBookingsToEventsPeriodsTable::getTableName();

        try {
            $statement = $this->connection->prepare(
                "SELECT 
                cb.id, cb.token
                FROM {$this->table} cb
                INNER JOIN {$customerBookingsEventsPeriods} cbep ON cbep.customerBookingId = cb.id
                INNER JOIN {$eventsPeriodsTable} ep ON ep.id = cbep.eventPeriodId 
                WHERE ep.eventId = :id"
            );

            $statement->execute([':id' => $id]);

            $rows = $statement->fetchAll();
        } catch (Exception $e) {
            throw new QueryExecutionException('Unable to return customer booking from' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $rows;
    }

    public function getQrTicketNumber($ticketNo)
    {
        try {
            $params[':ticketNo'] = '%' . $ticketNo . '%';

            $statement = $this->connection->prepare(
                "SELECT
                id,
                qrCodes
                FROM {$this->table} cb
                WHERE qrCodes LIKE :ticketNo"
            );

            $statement->execute($params);

            $row = $statement->fetch();

            return $row ?: null;
        } catch (Exception $e) {
            throw new QueryExecutionException('Unable to return customer bookings from ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function getById($id)
    {
        $params = [
            ':id' => $id,
        ];

        $paymentsTable = PaymentsTable::getTableName();

        $usersTable = UsersTable::getTableName();

        $couponsTable = CouponsTable::getTableName();

        try {
            $statement = $this->connection->prepare(
                "SELECT
                    cb.id AS booking_id,
                    cb.appointmentId AS booking_appointmentId,
                    cb.customerId AS booking_customerId,
                    cb.status AS booking_status,
                    cb.price AS booking_price,
                    cb.persons AS booking_persons,
                    cb.couponId AS booking_couponId,
                    cb.customFields AS booking_customFields,
                    cb.info AS booking_info,
                    cb.utcOffset AS booking_utcOffset,
                    cb.aggregatedPrice AS booking_aggregatedPrice,
                    cb.duration AS booking_duration,
                    cb.created AS booking_created,
                    cb.token AS booking_token,
                    
                    cb.qrCodes AS booking_qrCodes,
                    cu.id AS customer_id,
                    cu.firstName AS customer_firstName,
                    cu.lastName AS customer_lastName,
                    cu.email AS customer_email,
                    cu.note AS customer_note,
                    cu.phone AS customer_phone,
                    cu.countryPhoneIso AS customer_countryPhoneIso,
                    cu.gender AS customer_gender,
                    cu.birthday AS customer_birthday,
       
                    p.id AS payment_id,
                    p.amount AS payment_amount,
                    p.dateTime AS payment_dateTime,
                    p.status AS payment_status,
                    p.gateway AS payment_gateway,
                    p.gatewayTitle AS payment_gatewayTitle,
                    p.transactionId AS payment_transactionId,
                    p.data AS payment_data,
                    p.created AS payment_created,
                    
                    c.id AS coupon_id,
                    c.code AS coupon_code,
                    c.discount AS coupon_discount,
                    c.deduction AS coupon_deduction,
                    c.expirationDate AS coupon_expirationDate,
                    c.startDate AS coupon_startDate,
                    c.limit AS coupon_limit,
                    c.customerLimit AS coupon_customerLimit,
                    c.status AS coupon_status
                FROM {$this->table} cb
                INNER JOIN {$usersTable} cu ON cu.id = cb.customerId
                LEFT JOIN {$couponsTable} c ON c.id = cb.couponId
                LEFT JOIN {$paymentsTable} p ON p.customerBookingId = cb.id
                WHERE cb.id = :id"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (Exception $e) {
            throw new QueryExecutionException('Unable to find booking by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        $reformattedData = call_user_func([static::FACTORY, 'reformat'], $rows);

        return !empty($reformattedData[$id]) ?
            call_user_func([static::FACTORY, 'create'], $reformattedData[$id]) : null;
    }

    /**
     * Returns a collection of bookings where actions on booking are not completed
     *
     * @return Collection
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws \Exception
     */
    public function getUncompletedActionsForBookings()
    {
        $params = [];

        $currentDateTime = "STR_TO_DATE('" . DateTimeService::getNowDateTimeInUtc() . "', '%Y-%m-%d %H:%i:%s')";

        $pastDateTime =
            "STR_TO_DATE('" .
            DateTimeService::getNowDateTimeObjectInUtc()->modify('-1 day')->format('Y-m-d H:i:s') .
            "', '%Y-%m-%d %H:%i:%s')";

        try {
            $statement = $this->connection->prepare(
                "SELECT * FROM {$this->table} 
                WHERE
                      actionsCompleted = 0 AND
                      {$currentDateTime} > DATE_ADD(created, INTERVAL 300 SECOND) AND
                      {$pastDateTime} < created"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        $items = [];

        foreach ($rows as $row) {
            $items[] = call_user_func([static::FACTORY, 'create'], $row);
        }

        return new Collection($items);
    }

    /**
     * @param array $ids
     *
     * @return array
     * @throws QueryExecutionException
     */
    public function countByNoShowStatus($ids)
    {
        $idsString = implode(', ', $ids);

        try {
            $statement = $this->connection->prepare(
                "SELECT customerId, COUNT(*) AS count
                FROM {$this->table} cb
                WHERE customerId IN ($idsString) AND status = 'no-show'
                GROUP BY customerId"
            );

            $statement->execute();

            $rows = $statement->fetchAll();

            $result = [];
            foreach ($ids as $id) {
                $count = 0;
                foreach ($rows as $row) {
                    if ($row['customerId'] == $id) {
                        $count = $row['count'];
                        break;
                    }
                }
                $result[$id] = [
                    'id' => $id,
                    'count' => (int)$count,
                ];
            }
        } catch (Exception $e) {
            throw new QueryExecutionException('Unable to find booking by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }

    /**
     * @param array $criteria
     *
     * @return Collection
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function getByCriteria($criteria)
    {
        try {
            $params = [];

            $where = [];

            if (!empty($criteria['appointmentIds'])) {
                $queryAppointments = [];

                foreach ($criteria['appointmentIds'] as $index => $value) {
                    $param = ':appointmentId' . $index;

                    $queryAppointments[] = $param;

                    $params[$param] = $value;
                }

                $where[] = 'cb.appointmentId IN (' . implode(', ', $queryAppointments) . ')';
            }

            $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $statement = $this->connection->prepare(
                "SELECT
                    cb.id AS id,
                    cb.appointmentId AS appointmentId,
                    cb.customerId AS customerId,
                    cb.status AS status,
                    cb.price AS price,
                    cb.tax AS tax,
                    cb.persons AS persons,
                    cb.customFields AS customFields,
                    cb.info AS info,
                    cb.aggregatedPrice AS aggregatedPrice,
                    cb.packageCustomerServiceId AS packageCustomerServiceId,
                    cb.duration AS duration,
                    cb.created AS created,
                    cb.qrCodes AS qrCodes,
                    cb.tax AS tax
                FROM {$this->table} cb
                {$where}"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        $result = new Collection();

        foreach ($rows as $row) {
            $result->addItem(
                call_user_func([static::FACTORY, 'create'], $row),
                $row['id']
            );
        }

        return $result;
    }

    /**
     * @param array $criteria
     * @param int $limitPerPage
     *
     * @return array
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function getEventBookingIdsByCriteria($criteria = [], $limitPerPage = null)
    {
        $eventsPeriodsTable            = EventsPeriodsTable::getTableName();
        $customerBookingsEventsPeriods = CustomerBookingsToEventsPeriodsTable::getTableName();
        $eventsTable         = EventsTable::getTableName();
        $eventProvidersTable = EventsProvidersTable::getTableName();
        $customersTable = UsersTable::getTableName();

        $params = [];

        $where = [];

        $joins = [];

        if (!empty($criteria['customers'])) {
            $queryIds = [];

            foreach ($criteria['customers'] as $index => $value) {
                $param = ':customerId' . $index;

                $queryIds[] = $param;

                $params[$param] = $value;
            }

            $where[] = '(cb.customerId IN (' . implode(', ', $queryIds) . '))';
        }


        if (!empty($criteria['dates'])) {
            if (isset($criteria['dates'][0], $criteria['dates'][1])) {
                $where[] = "(ep.periodStart BETWEEN :eventFrom AND :eventTo)";
                $params[':eventFrom'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][0]);
                $params[':eventTo']   = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][1]);
            }
        }

        if (!empty($criteria['search'])) {
            $terms = preg_split('/\s+/', trim($criteria['search']));
            $termIndex = 0;

            foreach ($terms as $term) {
                $param = ":search{$termIndex}";
                $params[$param] = "%{$term}%";

                $where[] = "(
                        e.name LIKE {$param}
                        OR SUBSTR(cb.token, 1, 5) LIKE {$param}
                        OR CONCAT(cu.firstName, ' ', cu.lastName) LIKE {$param}
                    )";

                $termIndex++;
            }
        }

        if (!empty($criteria['providers'])) {
            $queryIds1 = [];
            $queryIds2 = [];

            foreach ($criteria['providers'] as $index => $value) {
                $param1 = ':providerId' . $index;
                $param2 = ':organizerId' . $index;

                $queryIds1[] = $param1;
                $queryIds2[] = $param2;

                $params[$param1] = $value;
                $params[$param2] = $value;
            }

            $where[] = '(epr.userId IN (' . implode(', ', $queryIds1) . ') OR e.organizerId IN (' . implode(', ', $queryIds2) . '))';

            $joins[] = "LEFT JOIN {$eventProvidersTable} epr ON epr.eventId = e.id";
        }

        if (!empty($criteria['statuses'])) {
            $queryIds = [];

            foreach ($criteria['statuses'] as $index => $value) {
                $param = ':status' . $index;

                $queryIds[] = $param;

                $params[$param] = $value;
            }

            $where[] = '(cb.status IN (' . implode(', ', $queryIds) . '))';
        }

        if (!empty($criteria['status'])) {
            $whereOr = [];
            foreach ($criteria['status'] as $index => $value) {
                switch ($value) {
                    case 'approved':
                        $whereOr[] = "(cb.status = 'approved' AND e.status = 'approved')";
                        break;
                    case 'canceled':
                        $whereOr[] = "(cb.status = 'canceled' OR e.status = 'rejected' OR e.status = 'canceled')";
                        break;
                    case 'rejected':
                        $whereOr[] = "(cb.status = 'rejected')";
                        break;
                    case 'no-show':
                        $whereOr[] = "(cb.status = 'no-show' and e.status = 'approved')";
                        break;
                    case 'waiting':
                        $whereOr[] = "(cb.status = 'waiting' and e.status = 'approved')";
                        break;
                }
            }
            $where[] = '(' . implode(' OR ', $whereOr) . ')';
        }

        if (!empty($criteria['events'])) {
            $queryIds = [];

            foreach ($criteria['events'] as $index => $value) {
                $param = ':eventId' . $index;

                $queryIds[] = $param;

                $params[$param] = $value;
            }

            $where[] = '(e.id IN (' . implode(', ', $queryIds) . '))';
        }

        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $groupBy = 'GROUP BY cb.id';
        $limit   = $this->getLimit(
            !empty($criteria['page']) ? (int)$criteria['page'] : 0,
            $limitPerPage
        );

        $orderBy = 'ORDER BY MIN(ep.periodStart), cb.id';

        if (!empty($criteria['sort'])) {
            $column      = $criteria['sort'][0] === '-' ? substr($criteria['sort'], 1) : $criteria['sort'];
            $orderDir = $criteria['sort'][0] === '-' ? 'DESC' : 'ASC';

            if ($column === 'attendee') {
                $joins[] = "INNER JOIN {$customersTable} cu ON cu.id = cb.customerId ";
                $orderBy  = "ORDER BY MIN(DATE(ep.periodStart)), CONCAT(cu.firstName, \" \", cu.lastName) {$orderDir}, cb.id";
            } elseif ($column === 'event') {
                $orderBy  = "ORDER BY MIN(DATE(ep.periodStart)), e.name {$orderDir}, cb.id";
            } elseif ($column === 'created') {
                $groupBy = 'GROUP BY cb.id, cb.created';
                $orderBy  = "ORDER BY cb.created {$orderDir}, cb.id";
            }
        }

        // TODO: refactor JOIN
        if (
            !empty($criteria['search']) &&
            (
                empty($criteria['sort']) ||
                ($criteria['sort'][0] === '-' ? substr($criteria['sort'], 1) : $criteria['sort']) !== 'attendee'
            )
        ) {
            $joins[] = "INNER JOIN {$customersTable} cu ON cu.id = cb.customerId ";
        }

        $joins = $joins ? implode(' ', $joins) : '';

        try {
            $statement = $this->connection->prepare(
                "SELECT cb.id
                FROM {$this->table} cb
                INNER JOIN {$customerBookingsEventsPeriods} cbe ON cbe.customerBookingId = cb.id
                LEFT JOIN {$eventsPeriodsTable} ep ON ep.id = cbe.eventPeriodId
                LEFT JOIN {$eventsTable} e ON e.id = ep.eventId
                
                {$joins}
                {$where}
                {$groupBy}
                {$orderBy}
                {$limit}"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll(Statement::FETCH_COLUMN);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find event by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
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
    public function getEventBookingsByIds($ids, $criteria)
    {
        $eventsPeriodsTable            = EventsPeriodsTable::getTableName();
        $customerBookingsEventsPeriods = CustomerBookingsToEventsPeriodsTable::getTableName();
        $usersTable           = UsersTable::getTableName();
        $eventsTable          = EventsTable::getTableName();
        $eventProvidersTable  = EventsProvidersTable::getTableName();
        $bookingsTicketsTable = CustomerBookingToEventsTicketsTable::getTableName();

        $params = [];

        $where = [];

        $fields = '';

        $joins = '';

        if (!empty($criteria['dates'])) {
            if (isset($criteria['dates'][0], $criteria['dates'][1])) {
                $where[] = "(ep.periodStart BETWEEN :eventFrom AND :eventTo)";
                $params[':eventFrom'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][0]);
                $params[':eventTo']   = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][1]);
            }
        }

        if (!empty($ids)) {
            $queryIds = [];

            foreach ($ids as $index => $value) {
                $param = ':id' . $index;

                $queryIds[] = $param;

                $params[$param] = $value;
            }

            $where[] = '(cb.id IN (' . implode(', ', $queryIds) . '))';
        }

        $orderBy = '';
        if (!empty($ids)) {
            $orderBy = 'ORDER BY FIELD(cb.id, ' . implode(', ', $queryIds) . ')';
        }

        if (!empty($criteria['fetchBookingsCoupons'])) {
            $couponsTable = CouponsTable::getTableName();

            $fields .= '
                c.id AS coupon_id,
                c.code AS coupon_code,
                c.discount AS coupon_discount,
                c.deduction AS coupon_deduction,
                c.limit AS coupon_limit,
                c.customerLimit AS coupon_customerLimit,
                c.status AS coupon_status,
            ';

            $joins .= "
                LEFT JOIN {$couponsTable} c ON c.id = cb.couponId
            ";
        }

        if (!empty($criteria['fetchBookingsPayments'])) {
            $paymentsTable = PaymentsTable::getTableName();

            $fields .= '
                p.id AS payment_id,
                p.amount AS payment_amount,
                p.dateTime AS payment_dateTime,
                p.status AS payment_status,
                p.gateway AS payment_gateway,
                p.gatewayTitle AS payment_gatewayTitle,
                p.transactionId AS payment_transactionId,
                p.data AS payment_data,
                p.wcOrderId AS payment_wcOrderId,
                p.wcOrderItemId AS payment_wcOrderItemId,
            ';

            $joins .= "
                LEFT JOIN {$paymentsTable} p ON p.customerBookingId = cb.id
            ";
        }

        if (!empty($criteria['fetchEvent'])) {
            $fields .= '
                ep.id as event_periodId,
                ep.periodStart as event_periodStart,
                ep.periodEnd as event_periodEnd,
                ep.zoomMeeting as event_zoomMeeting,
                ep.googleMeetUrl as event_googleMeetUrl,
                ep.microsoftTeamsUrl as event_microsoftTeamsUrl,
                ep.lessonSpace as event_lessonSpace,
                
                e.id AS event_id,
                e.name AS event_name,
                e.customPricing AS event_customPricing,
                e.status AS event_status,
                e.organizerId AS event_organizerId,
                e.settings AS event_settings,
            ';

            $joins .= "
                INNER JOIN {$customerBookingsEventsPeriods} cbe ON cbe.customerBookingId = cb.id
                LEFT JOIN {$eventsPeriodsTable} ep ON ep.id = cbe.eventPeriodId
                LEFT JOIN {$eventsTable} e ON e.id = ep.eventId
            ";
        }

        if (!empty($criteria['fetchProviders'])) {
            $fields .= '
                pu.id AS provider_id,
                pu.firstName AS provider_firstName,
                pu.lastName AS provider_lastName,
                pu.email AS provider_email,
                pu.pictureThumbPath AS provider_pictureThumbPath,
                pu.badgeId AS provider_badgeId,
            ';
            $joins  .= "
                LEFT JOIN {$eventProvidersTable} epr ON epr.eventId = e.id
                LEFT JOIN {$usersTable} pu ON epr.userId = pu.id or pu.id = e.organizerId
            ";
        }

        if (!empty($criteria['fetchCustomers'])) {
            $fields .= '
                cu.id AS customer_id,
                cu.type AS customer_type,
                cu.firstName AS customer_firstName,
                cu.lastName AS customer_lastName,
                cu.email AS customer_email,
                cu.note AS customer_note,
                cu.phone AS customer_phone,
                cu.countryPhoneIso AS customer_countryPhoneIso,
                cu.gender AS customer_gender,
                cu.birthday AS customer_birthday,
            ';

            $joins .= "
                INNER JOIN {$usersTable} cu ON cu.id = cb.customerId
            ";
        }

        $orderBy = '';

        if (!empty($criteria['sort'])) {
            $column = $criteria['sort'][0] === '-' ? substr($criteria['sort'], 1) : $criteria['sort'];

            $orderColumn = '';

            if ($column === 'attendee' && !empty($criteria['fetchCustomers'])) {
                $orderColumn = 'CONCAT(cu.firstName, " ", cu.lastName)';
            } elseif ($column === 'status') {
                $orderColumn = 'cb.status';
            }

            $orderDir = $orderColumn ? ($criteria['sort'][0] === '-' ? 'DESC' : 'ASC') : '';

            $orderBy = $orderColumn ? "ORDER BY {$orderColumn} {$orderDir}" : '';
        }

        $fields .= '
            cb.id AS booking_id,
            cb.appointmentId AS booking_appointmentId,
            cb.customerId AS booking_customerId,
            cb.status AS booking_status,
            cb.price AS booking_price,
            cb.tax AS booking_tax,
            cb.persons AS booking_persons,
            cb.couponId AS booking_couponId,
            cb.customFields AS booking_customFields,
            cb.info AS booking_info,
            cb.utcOffset AS booking_utcOffset,
            cb.token AS booking_token,
            cb.aggregatedPrice AS booking_aggregatedPrice,
            cb.qrCodes AS booking_qrCodes,
            cb.created AS booking_created,
            
            cbt.id AS booking_ticket_id,
            cbt.eventTicketId AS booking_ticket_eventTicketId,
            cbt.price AS booking_ticket_price,
            cbt.persons AS booking_ticket_persons
        ';

        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        try {
            $statement = $this->connection->prepare(
                "SELECT
                {$fields}
                FROM {$this->table} cb
                LEFT JOIN {$bookingsTicketsTable} cbt ON cbt.customerBookingId = cb.id
                {$joins}
                {$where}
                {$orderBy}
                "
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find event by id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        return CustomerBookingFactory::reformat($rows);
    }

    /**
     * Get appointment bookings by package customer ID
     *
     * @throws QueryExecutionException|InvalidArgumentException
     */
    public function getByPackageCustomerId(int $packageCustomerId): array
    {
        $packagesCustomersServicesTable = PackagesCustomersServicesTable::getTableName();

        try {
            $statement = $this->connection->prepare(
                "SELECT 
                    cb.appointmentId as appointmentId,
                    cb.id as id,
                    cb.status as status
                FROM {$this->table} cb
                INNER JOIN {$packagesCustomersServicesTable} pcs ON pcs.id = cb.packageCustomerServiceId
                WHERE pcs.packageCustomerId = :packageCustomerId
                AND cb.appointmentId IS NOT NULL"
            );

            $statement->execute([':packageCustomerId' => $packageCustomerId]);

            return $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get bookings by package customer id in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
