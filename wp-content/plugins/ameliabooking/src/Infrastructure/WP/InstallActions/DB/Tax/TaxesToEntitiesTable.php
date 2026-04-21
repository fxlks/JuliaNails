<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\InstallActions\DB\Tax;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\AbstractDatabaseTable;

/**
 * Class TaxesToEntitiesTable
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions\DB\Tax
 */
class TaxesToEntitiesTable extends AbstractDatabaseTable
{
    public const TABLE = 'taxes_to_entities';

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
                   `taxId` int(11) NOT NULL,
                   `entityId` int(11) NOT NULL,
                   `entityType` ENUM('service', 'extra', 'event', 'package') NOT NULL,
                    PRIMARY KEY (`id`)
                ) {$charsetCollate};";
    }
}
