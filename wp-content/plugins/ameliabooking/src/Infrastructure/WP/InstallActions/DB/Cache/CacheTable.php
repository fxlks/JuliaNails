<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\InstallActions\DB\Cache;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\AbstractDatabaseTable;

/**
 * Class CacheTable
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions\DB\Cache
 */
class CacheTable extends AbstractDatabaseTable
{
    public const TABLE = 'cache';

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public static function buildTable()
    {
        $table = self::getTableName();

        $charsetCollate = self::getCharsetCollate();

        return "CREATE TABLE {$table} (
                   `id` int(11) NOT NULL AUTO_INCREMENT,
                   `name` VARCHAR(255) NOT NULL,
                   `paymentId` INT(11) DEFAULT NULL,
                   `data` TEXT NULL DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) {$charsetCollate};";
    }
}
