<?php

namespace AmeliaBooking\Domain\ValueObjects\String;

final class DayOffType
{
    public const BLOCK_TIME   = 'blockTime';
    public const DAY_OFF  = 'dayOff';

    /**
     * @var string
     */
    private string $type;

    /**
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getValue(): string
    {
        return $this->type;
    }
}
