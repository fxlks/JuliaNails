<?php

namespace AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\Provider;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\AbstractDatabaseTable;
use AmeliaBooking\Domain\ValueObjects\String\Name;

/**
 * Class ProvidersDayOffTable
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\Provider
 */
class ProvidersDayOffTable extends AbstractDatabaseTable
{
    public const TABLE = 'providers_to_daysoff';

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public static function buildTable()
    {
        $table = self::getTableName();

        $charsetCollate = self::getCharsetCollate();

        $name = Name::MAX_LENGTH;

        return "CREATE TABLE {$table}  (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `userId` int(11) NULL,
                  `name` varchar({$name}) NOT NULL,
                  `startDate` datetime NOT NULL,
                  `endDate` datetime NOT NULL,
                  `type` enum('dayOff','blockTime') NOT NULL DEFAULT 'dayOff',
                  `repeat` tinyint(1) NOT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `id` (`id`)
                ) {$charsetCollate};";
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public static function alterTable()
    {
        $table = self::getTableName();

        return [
            "ALTER TABLE {$table} MODIFY startDate datetime NOT NULL",
            "ALTER TABLE {$table} MODIFY endDate datetime NOT NULL",
            "ALTER TABLE {$table} MODIFY userId int(11) DEFAULT NULL",
        ];
    }
}
