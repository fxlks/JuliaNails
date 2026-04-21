<?php

namespace AmeliaBooking\Infrastructure\Services\Logger;

use AmeliaBooking\Domain\Services\Logger\LoggerInterface;

/**
 * Class WPLogger
 *
 * WordPress-based logger implementation using error_log()
 *
 * @package AmeliaBooking\Infrastructure\Services\Logger
 */
class WPLogger implements LoggerInterface
{
    private string $prefix;

    public function __construct(string $prefix = 'Amelia')
    {
        $this->prefix = $prefix;
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Internal method to log messages
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    private function log(string $level, string $message, array $context = []): void
    {
        $contextStr = !empty($context) ? ' | ' . json_encode($context) : '';
        error_log("[{$this->prefix}] [{$level}] {$message}{$contextStr}");
    }
}
