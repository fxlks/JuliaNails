<?php

namespace AmeliaBooking\Domain\ValueObjects\String;

/**
 * Class NotificationStatus
 *
 * @package AmeliaBooking\Domain\ValueObjects\String
 */
final class NotificationStatus
{
    public const ENABLED  = 'enabled';
    public const DISABLED = 'disabled';

    /**
     * @var string
     */
    private $status;

    /**
     * @param string $status
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * Return the status from the value object
     *
     * @return string
     */
    public function getValue()
    {
        return $this->status;
    }
}
