<?php

namespace AmeliaBooking\Domain\ValueObjects;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;

/**
 * Class DiscountFixedValue
 *
 * @package AmeliaBooking\Domain\ValueObjects
 */
final class DiscountFixedValue
{
    private float $value;

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    public function __construct($value)
    {
        if ($value === null) {
            throw new InvalidArgumentException('Discount can\'t be empty');
        }

        if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
            throw new InvalidArgumentException("Discount \"{$value}\" must be float");
        }

        if ($value < 0) {
            throw new InvalidArgumentException('Discount must be larger then or equal to 0');
        }

        $this->value = (float)$value;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
