<?php

namespace AmeliaBooking\Infrastructure\Repository\Notification;

use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Notification\Notification;
use AmeliaBooking\Domain\Factory\Notification\NotificationFactory;
use AmeliaBooking\Domain\Repository\Notification\NotificationRepositoryInterface;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;
use AmeliaBooking\Infrastructure\Repository\Booking\Event\EventRepository;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Notification\NotificationsToEntitiesTable;

/**
 * Class NotificationRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\Notification
 */
class NotificationRepository extends AbstractRepository implements NotificationRepositoryInterface
{
    public const FACTORY = NotificationFactory::class;

    public const CUSTOM = false;

    /**
     * @param Notification $entity
     *
     * @return int
     * @throws QueryExecutionException
     */
    public function add($entity)
    {
        $data = $entity->toArray();

        $params = [
            ':name'         => $data['name'],
            ':customName'   => $data['customName'],
            ':sendTo'       => $data['sendTo'],
            ':status'       => $data['status'],
            ':type'         => $data['type'],
            ':entity'       => $data['entity'],
            ':time'         => $data['time'],
            ':timeBefore'   => $data['timeBefore'],
            ':timeAfter'    => $data['timeAfter'],
            ':subject'      => $data['subject'],
            ':content'      => $data['content'],
            ':translations' => $data['translations'],
            ':sendOnlyMe'   => $data['sendOnlyMe'] ? 1 : 0,
            ':whatsAppTemplate' => $data['whatsAppTemplate'],
            ':minimumTimeBeforeBooking' => $data['minimumTimeBeforeBooking']
        ];

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO {$this->table} 
                (`name`, `customName`, `sendTo`, `status`, `type`, `entity`, `time`, `timeBefore`,
                 `timeAfter`, `subject`, `content`, `translations`, `sendOnlyMe`, `whatsAppTemplate`, `minimumTimeBeforeBooking`)
                VALUES (:name, :customName, :sendTo, :status, :type, :entity, :time, :timeBefore,
                        :timeAfter, :subject, :content, :translations, :sendOnlyMe, :whatsAppTemplate, :minimumTimeBeforeBooking)"
            );

            $statement->execute($params);

            return $this->connection->lastInsertId();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int          $id
     * @param Notification $entity
     *
     * @return mixed
     * @throws QueryExecutionException
     */
    public function update($id, $entity)
    {
        $data = $entity->toArray();

        $params = [
            ':name'         => $data['name'],
            ':customName'   => $data['customName'],
            ':status'       => $data['status'],
            ':time'         => $data['time'],
            ':timeBefore'   => $data['timeBefore'],
            ':timeAfter'    => $data['timeAfter'],
            ':subject'      => $data['subject'],
            ':content'      => $data['content'],
            ':translations' => $data['translations'],
            ':sendOnlyMe'   => $data['sendOnlyMe'] ? 1 : 0,
            ':whatsAppTemplate' => $data['whatsAppTemplate'],
            ':minimumTimeBeforeBooking' => $data['minimumTimeBeforeBooking'],
            ':id'           => $id,
        ];

        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table} SET 
                `name` = :name,
                `customName` = :customName,
                `status` = :status,
                `time` = :time,
                `timeBefore` = :timeBefore,
                `timeAfter` = :timeAfter,
                `subject` = :subject,
                `content` = :content,
                `translations` = :translations,
                `sendOnlyMe` = :sendOnlyMe,
                `whatsAppTemplate` = :whatsAppTemplate,
                `minimumTimeBeforeBooking` = :minimumTimeBeforeBooking
                WHERE id = :id"
            );

            $statement->execute($params);

            return true;
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to save data in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param bool $includeCustom Whether to include custom notifications
     * @return Collection
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function getAll($includeCustom = true)
    {
        // Only include custom notifications if both self::CUSTOM is true AND $includeCustom parameter is true
        $custom = (!self::CUSTOM || !$includeCustom) ? ' WHERE customName IS NULL' : '';

        try {
            $statement = $this->connection->query($this->selectQuery() . $custom);

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
     * @param $name
     * @param $type
     * @param bool $includeCustom Whether to include custom notifications (default: true for backward compatibility)
     *
     * @return Collection
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function getByNameAndType($name, $type, $includeCustom = true)
    {
        // Only include custom notifications if both self::CUSTOM is true AND $includeCustom parameter is true
        $custom = (!self::CUSTOM || !$includeCustom) ? 'customName IS NULL AND ' : '';

        try {
            $statement = $this->connection->prepare(
                $this->selectQuery() . " WHERE {$custom}{$this->table}.name LIKE :name AND {$this->table}.type = :type"
            );

            $params = [
                ':name' => $name,
                ':type' => $type
            ];

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by name and type in ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }

        $items = new Collection();
        foreach ($rows as $row) {
            $items->addItem(call_user_func([static::FACTORY, 'create'], $row), $row['id']);
        }

        return $items;
    }


    /**
     * @param int $notificationId
     *
     * @return bool
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function delete($notificationId)
    {
        $notificationsToEntities = NotificationsToEntitiesTable::getTableName();
        $params = [
            ':id'  => $notificationId,
        ];

        try {
            $statement = $this->connection->prepare(
                "DELETE FROM {$this->table} WHERE id = :id"
            );
            $statement->execute($params);
            $statement = $this->connection->prepare(
                "DELETE FROM {$notificationsToEntities} WHERE notificationId = :id"
            );
            $statement->execute($params);

            return true;
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to delete data from ' . __CLASS__ . '. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
