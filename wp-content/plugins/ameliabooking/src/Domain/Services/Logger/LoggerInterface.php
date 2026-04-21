<?php

namespace AmeliaBooking\Domain\Services\Logger;

/**
 * Interface LoggerInterface
 *
 * @package AmeliaBooking\Domain\Services\Logger
 */
interface LoggerInterface
{
    /** Log an informational message */
    public function info(string $message, array $context = []): void;

    /** Log an error message */
    public function error(string $message, array $context = []): void;

    /** Log a warning message */
    public function warning(string $message, array $context = []): void;

    /** Log a debug message */
    public function debug(string $message, array $context = []): void;
}
