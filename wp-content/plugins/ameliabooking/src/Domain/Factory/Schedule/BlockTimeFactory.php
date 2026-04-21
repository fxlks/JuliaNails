<?php

namespace AmeliaBooking\Domain\Factory\Schedule;

use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Entity\Schedule\BlockTime;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Factory\User\UserFactory;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\ValueObjects\DateTime\DateTimeValue;
use AmeliaBooking\Domain\ValueObjects\Number\Integer\Id;
use AmeliaBooking\Domain\ValueObjects\String\Name;
use DateTimeZone;

class BlockTimeFactory
{
    /**
     * @param $data
     * @return BlockTime
     * @throws InvalidArgumentException
     */
    public static function create($data): BlockTime
    {
        $blockTime = new BlockTime(
            new Name($data['name']),
            $data['userId'] ? new Id($data['userId']) : null,
            new DateTimeValue(DateTimeService::getCustomDateTimeObject($data['startDate'])),
            new DateTimeValue(DateTimeService::getCustomDateTimeObject($data['endDate'])),
        );

        if (isset($data['id'])) {
            $blockTime->setId(new Id($data['id']));
        }

        if (isset($data['user'])) {
            $blockTime->setUser(UserFactory::create($data['user']));
        }

        return $blockTime;
    }


    /**
     * @param array $rows
     *
     * @return Collection
     * @throws InvalidArgumentException
     */
    public static function createCollection($rows)
    {
        $blockTimes = [];

        foreach ($rows as $row) {
            $startDate = $row['startDate'];
            $endDate = $row['endDate'];
            $userId = isset($row['userId']) ? $row['userId'] : null;

            $blockTimes[$row['id']] = [
                'id'        => $row['id'],
                'userId'    => $row['userId'],
                'name'      => $row['name'],
                'startDate' => DateTimeService::getCustomDateTimeFromUtc($startDate),
                'endDate'   => DateTimeService::getCustomDateTimeFromUtc($endDate),
            ];

            if ($userId) {
                $blockTimes[$row['id']]['user'] =
                    [
                        'id'        => $userId,
                        'firstName' => $row['user_firstName'],
                        'lastName'  => $row['user_lastName'],
                        'fullName'  => $row['user_fullName'],
                        'type'      => 'provider',
                    ];
            }
        }

        $collection = new Collection();

        foreach ($blockTimes as $key => $value) {
            $collection->addItem(
                self::create($value),
                $key
            );
        }

        return $collection;
    }
}
