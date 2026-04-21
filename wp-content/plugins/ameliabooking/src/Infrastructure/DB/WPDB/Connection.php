<?php

namespace AmeliaBooking\Infrastructure\DB\WPDB;

use AmeliaBooking\Domain\Services\Database\ConnectionInterface;
use wpdb;

/**
 * Class Connection
 *
 * @package AmeliaBooking\Infrastructure\DB\WPDB
 * @property \wpdb $wpdb
 */
class Connection implements ConnectionInterface
{
    /** @var wpdb */
    private $wpdb;

    /**
     * @param wpdb $wpdb
     */
    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;

        // Enable SQL_BIG_SELECTS to handle complex JOINs on restrictive hosting
        $this->wpdb->query('SET SESSION SQL_BIG_SELECTS=1');

        $this->wpdb->query("SET NAMES " . (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4'));
    }

    /**
     * @param string $statement
     *
     * @return Statement
     */
    public function query($statement)
    {
        $stmt = new Statement($this->wpdb, $statement);
        $stmt->execute();

        return $stmt;
    }

    /**
     * @param string $statement
     *
     * @return Statement
     */
    public function prepare($statement)
    {
        return new Statement($this->wpdb, $statement);
    }

    /**
     * @return int
     */
    public function lastInsertId()
    {
        return $this->wpdb->insert_id;
    }

    /**
     * @return void
     */
    public function beginTransaction()
    {
        $this->wpdb->query('START TRANSACTION');
    }

    /**
     * @return void
     */
    public function commit()
    {
        $this->wpdb->query('COMMIT');
    }

    /**
     * @return void
     */
    public function rollBack()
    {
        $this->wpdb->query('ROLLBACK');
    }

    /**
     * Allow the connection wrapper to be used as a callable (historical usage in repositories `$connection()`).
     * Returning $this preserves backward compatibility without altering repository constructors.
     *
     * @return $this
     */
    public function __invoke()
    {
        return $this;
    }
}
