<?php

namespace AmeliaBooking\Infrastructure\DB\WPDB;

use wpdb;

class Statement
{
    public const FETCH_ASSOC = 'assoc';
    public const FETCH_COLUMN = 'column';

    /** @var wpdb */
    private $wpdb;

    /** @var string */
    private $statement;

    /** @var array */
    private $bindings = [];

    /** @var int */
    private $fetchIndex = 0;

    /** @var array|null Cached result set for fetch operations */
    private $results = null;

    /** @var int */
    private $affectedRows = 0;

    /**
     * @param wpdb   $wpdb
     * @param string $statement
     */
    public function __construct($wpdb, $statement)
    {
        $this->wpdb = $wpdb;
        $this->statement = $statement;
    }

    /**
     * @param int|string $parameter
     * @param mixed      $variable
     */
    public function bindValue($parameter, $variable)
    {
        $this->bindings[$parameter] = $variable;
    }

    /**
     * @param int|string $parameter
     * @param mixed      $variable
     */
    public function bindParam($parameter, $variable)
    {
        $this->bindValue($parameter, $variable);
    }

    /**
     * Execute the statement.
     * Accepts optional params array (e.g. [':id' => 5]).
     * Repositories previously called execute($params); this now works.
     *
     * @param array $params
     * @return void
     */
    public function execute(array $params = [])
    {
        // Merge passed params into bindings (allows both bindParam + execute($params))
        if ($params) {
            foreach ($params as $key => $value) {
                $this->bindings[$key] = $value;
            }
        }

        $query = $this->statement;

        if ($this->bindings) {
            // Sort bindings by key length, descending, to avoid replacing ":p1" with value of ":p10"
            uksort($this->bindings, function ($a, $b) {
                return strlen($b) - strlen($a);
            });

            foreach ($this->bindings as $placeholder => $value) {
                $replacement = 'NULL';

                if ($value !== null) {
                    $format = is_int($value) || is_bool($value) ? '%d' : (is_float($value) ? '%f' : '%s');
                    $replacement = $this->wpdb->prepare($format, $value);
                }

                $query = str_replace($placeholder, $replacement, $query);
            }
        }

        $this->fetchIndex = 0; // reset pointer for subsequent fetch() calls

        // Execute using query() - this handles all query types (SELECT, INSERT, UPDATE, etc.)
        // It populates $wpdb->last_result (for rows) and returns the count
        $result = $this->wpdb->query($query);

        if ($result === false) {
            $this->results = [];
            $this->affectedRows = 0;
            throw new \RuntimeException($this->wpdb->last_error ?: 'Database query failed.');
        }

        // Capture affected rows immediately so it's stable even if other WP queries run later
        $this->affectedRows = (int) $result;

        // If the query produced results (SELECT, SHOW, etc.), wpdb stores them in last_result as objects
        // Convert objects to arrays to match PDO::FETCH_ASSOC behavior
        $this->results = array_map(function ($row) {
            return (array) $row;
        }, $this->wpdb->last_result ?? []);
    }

    /**
     * @param string $fetchMode Fetch mode (FETCH_ASSOC or FETCH_COLUMN)
     * @return array
     */
    public function fetchAll(string $fetchMode = self::FETCH_ASSOC)
    {
        if (!$this->results) {
            return [];
        }

        // If FETCH_COLUMN, return only the first column from each row
        if ($fetchMode === self::FETCH_COLUMN) {
            return array_map(function ($row) {
                return reset($row); // Get first value from associative array
            }, $this->results);
        }

        return $this->results;
    }

    /**
     * @return array|false
     */
    public function fetch()
    {
        if ($this->results && isset($this->results[$this->fetchIndex])) {
            return $this->results[$this->fetchIndex++];
        }

        return false;
    }

    /**
     * @return int
     */
    public function rowCount()
    {
        return $this->affectedRows;
    }
}
