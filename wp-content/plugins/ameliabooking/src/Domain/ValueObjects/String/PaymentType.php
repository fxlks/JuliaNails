<?php

namespace AmeliaBooking\Domain\ValueObjects\String;

/**
 * Class PaymentType
 *
 * @package AmeliaBooking\Domain\ValueObjects\String
 */
final class PaymentType
{
    public const PAY_PAL = 'payPal';

    public const STRIPE = 'stripe';

    public const ON_SITE = 'onSite';

    public const WC = 'wc';

    public const MOLLIE = 'mollie';

    public const RAZORPAY = 'razorpay';

    public const SQUARE = 'square';

    public const BARION = 'barion';

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
