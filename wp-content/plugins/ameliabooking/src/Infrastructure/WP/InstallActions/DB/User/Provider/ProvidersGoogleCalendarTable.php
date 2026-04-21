<?php

namespace AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\Provider;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\ValueObjects\String\Email;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\AbstractDatabaseTable;

/**
 * Class ProvidersGoogleCalendarTable
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\Provider
 */
class ProvidersGoogleCalendarTable extends AbstractDatabaseTable
{
    public const TABLE = 'providers_to_google_calendar';

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public static function buildTable()
    {
        $table = self::getTableName();

        $charsetCollate = self::getCharsetCollate();

        $email = Email::MAX_LENGTH;

        return "CREATE TABLE {$table}  (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `userId` INT(11) NOT NULL,
                  `token` TEXT NOT NULL,
                  `calendarId` TEXT({$email}) NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `id` (`id`)
                ) {$charsetCollate};";
    }
}
