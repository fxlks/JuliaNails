<?php

namespace AmeliaBooking\Domain\Services\Database;

interface ConnectionInterface
{
    /**
     * @param string $statement
     * @return mixed
     */
    public function query($statement);

    /**
     * @param string $statement
     * @return mixed
     */
    public function prepare($statement);

    /**
     * @return int
     */
    public function lastInsertId();

    /**
     * @return void
     */
    public function beginTransaction();

    /**
     * @return void
     */
    public function commit();

    /**
     * @return void
     */
    public function rollBack();
}
