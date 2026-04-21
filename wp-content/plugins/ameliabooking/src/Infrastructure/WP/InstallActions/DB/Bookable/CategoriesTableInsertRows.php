<?php

namespace AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\AbstractDatabaseTable;
use AmeliaBooking\Infrastructure\WP\Translations\NotificationsStrings;

/**
 * Class CategoriesTableInsertRows
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable
 */
class CategoriesTableInsertRows extends AbstractDatabaseTable
{
    public const TABLE = 'categories';

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public static function buildTable()
    {
        global $wpdb;
        $table = self::getTableName();

        $addCategories = !(int)$wpdb->get_row("SELECT COUNT(*) AS count FROM {$table}")->count;

        if (!$addCategories) {
            return [];
        }

        return ["INSERT INTO {$table} 
            (
                `status`,
                `name`,
                `position`,
                `color`
            ) 
            VALUES
            (
                'visible',
                'Default',
                 1,
                 '#1a84ee'
            )"];
    }
}
