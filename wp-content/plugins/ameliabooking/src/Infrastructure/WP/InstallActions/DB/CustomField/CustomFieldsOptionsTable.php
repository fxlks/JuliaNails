<?php

namespace AmeliaBooking\Infrastructure\WP\InstallActions\DB\CustomField;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\AbstractDatabaseTable;

/**
 * Class CustomFieldsOptionsTable
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions\DB\CustomField
 */
class CustomFieldsOptionsTable extends AbstractDatabaseTable
{
    public const TABLE = 'custom_fields_options';

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public static function buildTable()
    {
        $table = self::getTableName();

        $charsetCollate = self::getCharsetCollate();

        return "CREATE TABLE {$table} (
                   `id` INT(11) NOT NULL AUTO_INCREMENT,
                   `customFieldId` int(11) NOT NULL,
                   `label` TEXT DEFAULT NULL,
                   `position` int(11) NOT NULL,
                   `translations` TEXT NULL DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) {$charsetCollate};";
    }
}
