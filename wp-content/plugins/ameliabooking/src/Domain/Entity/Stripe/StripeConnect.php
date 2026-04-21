<?php

namespace AmeliaBooking\Domain\Entity\Stripe;

use AmeliaBooking\Domain\ValueObjects\Number\Float\Price;
use AmeliaBooking\Domain\ValueObjects\String\Name;

/**
 * Class StripeConnect
 *
 * @package AmeliaBooking\Domain\Entity\Stripe
 */
class StripeConnect
{
    /** @var Name */
    private $id;

    /** @var Price */
    private $amount;

    /** @var Name */
    private $accountId;


    /**
     * @return Name
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Name $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Price
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param Price $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getAccountId()
    {
        return $this->accountId;
    }

    public function setAccountId(Name $accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id'          => $this->getId() ? $this->getId()->getValue() : null,
            'amount'      => $this->getAmount() ? $this->getAmount()->getValue() : null,
            'accountId'   => $this->getAccountId() ? $this->getAccountId()->getValue() : null,
        ];
    }
}
